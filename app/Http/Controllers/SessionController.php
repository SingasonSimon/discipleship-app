<?php

namespace App\Http\Controllers;

use App\Http\Requests\SessionRequest;
use App\Models\ClassSession;
use App\Models\DiscipleshipClass;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of sessions for a class.
     */
    public function index(DiscipleshipClass $class): View
    {
        // Allow viewing if user can manage classes OR is enrolled
        $user = auth()->user();
        $canView = false;
        
        if ($user->canManageClasses() || $user->id === $class->mentor_id) {
            $canView = true;
        } elseif ($user->isMember()) {
            $member = \App\Models\Member::where('user_id', $user->id)->first();
            if ($member) {
                $canView = $member->enrollments()
                    ->where('class_id', $class->id)
                    ->where('status', 'approved')
                    ->exists();
            }
        }
        
        if (!$canView) {
            abort(403, 'You must be enrolled in this class to view its sessions.');
        }

        $sessions = $class->sessions()
            ->with(['attendance.member'])
            ->orderBy('session_date', 'desc')
            ->paginate(20);

        return view('sessions.index', compact('class', 'sessions'));
    }

    /**
     * Show the form for creating a new session.
     */
    public function create(DiscipleshipClass $class): View
    {
        $this->authorize('manageSessions', $class);

        return view('sessions.create', compact('class'));
    }

    /**
     * Store a newly created session.
     */
    public function store(SessionRequest $request, DiscipleshipClass $class)
    {
        $this->authorize('manageSessions', $class);

        $session = ClassSession::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('sessions.show', $session)
            ->with('success', 'Class session created successfully.');
    }

    /**
     * Display the specified session.
     */
    public function show(ClassSession $session): View
    {
        // Load the class relationship since we're using shallow routing
        $session->load([
            'class.mentor',
            'attendance.member',
            'creator',
        ]);

        $class = $session->class;

        // If class is not loaded, try to load it explicitly
        if (!$class) {
            $class = DiscipleshipClass::with('mentor')->findOrFail($session->class_id);
        }

        // Check if user can view the session (policy handles enrolled members)
        $this->authorize('view', $session);

        // Get attendance statistics
        $attendanceStats = [
            'total_members' => $session->attendance()->count(),
            'present_count' => $session->attendance()->where('status', 'present')->count(),
            'absent_count' => $session->attendance()->where('status', 'absent')->count(),
            'excused_count' => $session->attendance()->where('status', 'excused')->count(),
        ];

        // Calculate attendance rate
        $attendanceStats['attendance_rate'] = $attendanceStats['total_members'] > 0
            ? round(($attendanceStats['present_count'] / $attendanceStats['total_members']) * 100, 2)
            : 0;

        return view('sessions.show', compact('class', 'session', 'attendanceStats'));
    }

    /**
     * Show the form for editing the specified session.
     */
    public function edit(ClassSession $session): View
    {
        $session->load('class.mentor');
        $class = $session->class;
        
        $this->authorize('manageSessions', $class);

        return view('sessions.edit', compact('class', 'session'));
    }

    /**
     * Update the specified session.
     */
    public function update(SessionRequest $request, ClassSession $session)
    {
        $session->load('class');
        $class = $session->class;
        
        $this->authorize('manageSessions', $class);

        $session->update($request->validated());

        return redirect()
            ->route('sessions.show', $session)
            ->with('success', 'Class session updated successfully.');
    }

    /**
     * Remove the specified session.
     */
    public function destroy(ClassSession $session)
    {
        $session->load('class');
        $class = $session->class;
        
        $this->authorize('manageSessions', $class);

        // Check if session has attendance records
        if ($session->attendance()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete session with attendance records. Please delete attendance records first.');
        }

        $classId = $class->id;
        $session->delete();

        return redirect()
            ->route('classes.show', $classId)
            ->with('success', 'Class session deleted successfully.');
    }

    /**
     * Show attendance marking interface for a session.
     */
    public function attendance(ClassSession $session): View
    {
        $session->load('class.mentor');
        $class = $session->class;
        
        $this->authorize('viewAttendance', $class);

        // Get all enrolled members who should attend this session
        $members = $class->enrolledMembers()
            ->where('class_enrollments.status', 'approved')
            ->with(['attendance' => function ($query) use ($session) {
                $query->where('class_session_id', $session->id);
            }])
            ->get();

        // Get existing attendance records
        $existingAttendance = $session->attendance()
            ->with('member')
            ->get()
            ->keyBy('member_id');

        return view('sessions.attendance', compact('class', 'session', 'members', 'existingAttendance'));
    }

    /**
     * Get upcoming sessions for a class.
     */
    public function upcoming(DiscipleshipClass $class): View
    {
        $this->authorize('view', $class);

        $upcomingSessions = $class->sessions()
            ->where('session_date', '>=', now())
            ->orderBy('session_date')
            ->get();

        return view('sessions.upcoming', compact('class', 'upcomingSessions'));
    }

    /**
     * Get session statistics for a class.
     */
    public function statistics(DiscipleshipClass $class): View
    {
        $this->authorize('view', $class);

        $sessions = $class->sessions()
            ->withCount('attendance')
            ->orderBy('session_date')
            ->get();

        $stats = [
            'total_sessions' => $sessions->count(),
            'total_attendance' => $sessions->sum('attendance_count'),
            'average_attendance' => $sessions->count() > 0 ? round($sessions->sum('attendance_count') / $sessions->count(), 2) : 0,
            'attendance_trend' => $this->calculateAttendanceTrend($sessions),
        ];

        return view('sessions.statistics', compact('class', 'sessions', 'stats'));
    }

    /**
     * Calculate attendance trend over time.
     */
    private function calculateAttendanceTrend($sessions): array
    {
        $trend = [];

        foreach ($sessions as $session) {
            $trend[] = [
                'date' => $session->session_date->format('Y-m-d'),
                'attendance' => $session->attendance_count,
            ];
        }

        return $trend;
    }

    /**
     * Send Google Meet link to all enrolled members via email.
     */
    public function sendGoogleMeetLink(ClassSession $session)
    {
        $session->load('class.mentor');
        $class = $session->class;
        
        $this->authorize('manageSessions', $class);

        if (!$session->google_meet_link) {
            return redirect()
                ->back()
                ->with('error', 'This session does not have a Google Meet link. Please add one first.');
        }

        // Get all enrolled and approved members
        $members = $class->enrolledMembers()
            ->where('class_enrollments.status', 'approved')
            ->whereNotNull('email')
            ->get();

        if ($members->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'No enrolled members with email addresses found for this class.');
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($members as $member) {
            try {
                // Create a simple notifiable object for the notification
                $notifiable = new class($member) {
                    public function __construct(private $member) {}
                    
                    public function routeNotificationForMail(): string
                    {
                        return $this->member->email;
                    }
                    
                    public function getAttribute($name)
                    {
                        return $this->member->$name ?? null;
                    }
                    
                    public function __get($name)
                    {
                        return $this->getAttribute($name);
                    }
                };
                
                // Send notification via mail channel
                $notifiable->notify(new \App\Notifications\SessionGoogleMeetLinkNotification($session));
                $sentCount++;
            } catch (\Exception $e) {
                $failedCount++;
                \Log::error("Failed to send Google Meet link to member {$member->id}: " . $e->getMessage());
            }
        }

        $message = "Google Meet link sent to {$sentCount} member(s).";
        if ($failedCount > 0) {
            $message .= " Failed to send to {$failedCount} member(s).";
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }
}
