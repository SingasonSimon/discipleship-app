<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassRequest;
use App\Models\DiscipleshipClass;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // All authenticated users can view classes list (members can browse, mentors can manage)
        $query = DiscipleshipClass::with(['mentor', 'sessions']);

        // Role-based filtering: Pastors and mentors should only see their own classes
        $user = auth()->user();
        if ($user->isPastor() || $user->isMentor()) {
            $query->where('mentor_id', $user->id);
        }
        // Admins can see all classes (no filter)

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('mentor', function ($mentorQuery) use ($search) {
                      $mentorQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by mentor (only for admins, as pastors/mentors already filtered)
        if ($user->isAdmin() && $request->filled('mentor_id')) {
            $query->where('mentor_id', $request->get('mentor_id'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_active', true)
                      ->where(function ($q) {
                          $q->whereNull('end_date')
                            ->orWhere('end_date', '>=', now());
                      });
            } elseif ($status === 'completed') {
                $query->where('is_active', false)
                      ->orWhere('end_date', '<', now());
            }
        }

        // Filter by schedule type
        if ($request->filled('schedule_type')) {
            $query->where('schedule_type', $request->get('schedule_type'));
        }

        $classes = $query->orderBy('created_at', 'desc')
                        ->paginate(15)
                        ->withQueryString();

        // Get mentors for filter dropdown
        $mentors = User::whereIn('role', ['admin', 'pastor', 'mentor'])
                      ->orderBy('name')
                      ->get();

        return view('classes.index', compact('classes', 'mentors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', DiscipleshipClass::class);
        
        $mentors = User::whereIn('role', ['admin', 'pastor', 'mentor'])
                      ->orderBy('name')
                      ->get();

        return view('classes.create', compact('mentors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClassRequest $request)
    {
        $this->authorize('create', DiscipleshipClass::class);
        
        $validated = $request->validated();
        $weeklyTopics = $validated['weekly_topics'] ?? [];
        $weeklyContent = $validated['weekly_content'] ?? [];
        
        // Remove weekly topics/content from validated data before creating class
        unset($validated['weekly_topics'], $validated['weekly_content']);
        
        $classOutline = $validated['class_outline'] ?? null;
        unset($validated['class_outline']);
        
        $class = DiscipleshipClass::create($validated);

        // Create class outline as the first content item if provided
        if (!empty($classOutline)) {
            \App\Models\ClassContent::create([
                'class_id' => $class->id,
                'title' => 'Class Outline',
                'content' => $classOutline,
                'content_type' => \App\Models\ClassContent::TYPE_OUTLINE,
                'week_number' => null,
                'order' => 0,
                'is_published' => true,
                'created_by' => auth()->id(),
            ]);
        }

        // Generate sessions with weekly topics if provided
        if (!empty($weeklyTopics)) {
            $this->generateSessionsWithTopics($class, $weeklyTopics, $weeklyContent);
        } else {
            // Generate sessions with default topics if no topics provided
            $class->generateSessions();
        }

        return redirect()
            ->route('classes.show', $class)
            ->with('success', 'Discipleship class created successfully with ' . $class->sessions()->count() . ' sessions.');
    }

    /**
     * Generate sessions with custom weekly topics
     */
    private function generateSessionsWithTopics(DiscipleshipClass $class, array $topics, array $content = []): void
    {
        if (!$class->start_date) {
            return;
        }

        $startDate = \Carbon\Carbon::parse($class->start_date);
        $endDate = $class->end_date ? \Carbon\Carbon::parse($class->end_date) : $startDate->copy()->addWeeks($class->duration_weeks);
        
        $targetDay = $class->schedule_day ? strtolower($class->schedule_day) : null;
        
        $sessionNumber = 1;
        $currentDate = $startDate->copy();
        $topicIndex = 0;

        while ($currentDate <= $endDate && $topicIndex < count($topics)) {
            // If a specific day is set, move to that day of the week
            if ($targetDay) {
                $dayMap = [
                    'monday' => \Carbon\Carbon::MONDAY,
                    'tuesday' => \Carbon\Carbon::TUESDAY,
                    'wednesday' => \Carbon\Carbon::WEDNESDAY,
                    'thursday' => \Carbon\Carbon::THURSDAY,
                    'friday' => \Carbon\Carbon::FRIDAY,
                    'saturday' => \Carbon\Carbon::SATURDAY,
                    'sunday' => \Carbon\Carbon::SUNDAY,
                ];
                
                $targetDayOfWeek = $dayMap[$targetDay] ?? null;
                if ($targetDayOfWeek && $currentDate->dayOfWeek !== $targetDayOfWeek) {
                    $currentDate->next($targetDayOfWeek);
                    if ($currentDate > $endDate) {
                        break;
                    }
                }
            }

            // Create session based on schedule type
            $shouldCreate = false;
            $nextInterval = 1; // weeks
            
            if ($class->schedule_type === 'weekly') {
                $shouldCreate = true;
            } elseif ($class->schedule_type === 'biweekly') {
                $shouldCreate = ($sessionNumber % 2 === 1);
                $nextInterval = 2;
            } elseif ($class->schedule_type === 'monthly') {
                $shouldCreate = true;
                $nextInterval = 1; // months
            } else {
                $shouldCreate = true;
            }

            if ($shouldCreate && $currentDate <= $endDate && isset($topics[$topicIndex]) && !empty($topics[$topicIndex])) {
                $class->sessions()->create([
                    'session_date' => $currentDate->toDateString(),
                    'topic' => $topics[$topicIndex],
                    'notes' => $content[$topicIndex] ?? null,
                    'location' => $class->location,
                    'created_by' => $class->mentor_id,
                ]);
                $sessionNumber++;
                $topicIndex++;
            }

            // Move to next session date
            if ($class->schedule_type === 'monthly') {
                $currentDate->addMonth();
            } else {
                $currentDate->addWeeks($nextInterval);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DiscipleshipClass $class): View
    {
        // All authenticated users can view individual classes
        
        $class->load([
            'mentor',
            'sessions' => function ($query) {
                $query->orderBy('session_date', 'desc');
            },
            'sessions.attendance.member',
        ]);

        // Get class statistics
        $stats = [
            'total_sessions' => $class->sessions()->count(),
            'upcoming_sessions' => $class->sessions()
                ->where('session_date', '>=', now())
                ->count(),
            'total_attendance' => $class->sessions()
                ->withCount('attendance')
                ->get()
                ->sum('attendance_count'),
            'average_attendance' => $this->calculateAverageAttendance($class),
        ];

        // Get recent sessions (last 5)
        $recentSessions = $class->sessions()
            ->with(['attendance.member'])
            ->orderBy('session_date', 'desc')
            ->take(5)
            ->get();

        $orderedContents = $class->orderedPublishedContents()->get();
        $contentGroups = $orderedContents->groupBy('week_number');
        $totalPublishedLessons = $orderedContents->count();

        $memberEnrollment = null;
        $contentProgressStates = [];

        if (auth()->check() && auth()->user()->isMember()) {
            $member = Member::where('user_id', auth()->id())->first();
            if ($member) {
                $enrollment = $member->enrollments()
                    ->where('class_id', $class->id)
                    ->where('status', 'approved')
                    ->first();

                if ($enrollment) {
                    $memberEnrollment = $enrollment;
                    $contentProgressStates = $enrollment->buildProgressStates($orderedContents);
                }
            }
        }

        return view('classes.show', [
            'class' => $class,
            'stats' => $stats,
            'recentSessions' => $recentSessions,
            'groupedContents' => $contentGroups,
            'memberEnrollment' => $memberEnrollment,
            'contentProgressStates' => $contentProgressStates,
            'totalPublishedLessons' => $totalPublishedLessons,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DiscipleshipClass $class): View
    {
        $this->authorize('update', $class);
        
        $mentors = User::whereIn('role', ['admin', 'pastor', 'mentor'])
                      ->orderBy('name')
                      ->get();

        return view('classes.edit', compact('class', 'mentors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClassRequest $request, DiscipleshipClass $class)
    {
        $this->authorize('update', $class);
        
        $class->update($request->validated());

        return redirect()
            ->route('classes.show', $class)
            ->with('success', 'Discipleship class updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DiscipleshipClass $class)
    {
        $this->authorize('delete', $class);
        
        // Check if class has sessions
        if ($class->sessions()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete class with existing sessions. Please delete all sessions first.');
        }

        $class->delete();

        return redirect()
            ->route('classes.index')
            ->with('success', 'Discipleship class deleted successfully.');
    }

    /**
     * Toggle class active status
     */
    public function toggleStatus(DiscipleshipClass $class)
    {
        $this->authorize('update', $class);

        $class->update(['is_active' => ! $class->is_active]);

        $status = $class->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', "Class {$status} successfully.");
    }

    /**
     * Get class schedule
     */
    public function schedule(DiscipleshipClass $class): View
    {
        $this->authorize('view', $class);

        $class->load(['sessions' => function ($query) {
            $query->orderBy('session_date');
        }]);

        return view('classes.schedule', compact('class'));
    }

    /**
     * Calculate average attendance for a class
     */
    private function calculateAverageAttendance(DiscipleshipClass $class): float
    {
        $sessions = $class->sessions()->withCount('attendance')->get();

        if ($sessions->isEmpty()) {
            return 0;
        }

        $totalAttendance = $sessions->sum('attendance_count');
        $totalSessions = $sessions->count();

        return round($totalAttendance / $totalSessions, 2);
    }
}
