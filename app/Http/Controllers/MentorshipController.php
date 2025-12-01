<?php

namespace App\Http\Controllers;

use App\Http\Requests\MentorshipRequest;
use App\Models\Member;
use App\Models\Mentorship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MentorshipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (! $request->user()->hasAnyRole(['admin', 'pastor', 'mentor'])) {
                abort(403, 'Access denied. Only admins, pastors, or mentors can view mentorships.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of mentorships.
     */
    public function index(Request $request): View
    {
        $query = Mentorship::with(['member', 'mentor']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('member', function ($memberQuery) use ($search) {
                    $memberQuery->where('full_name', 'like', "%{$search}%")
                               ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('mentor', function ($mentorQuery) use ($search) {
                    $mentorQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by mentor
        if ($request->filled('mentor_id')) {
            $query->where('mentor_id', $request->get('mentor_id'));
        }

        // Filter by date range
        if ($request->filled('start_date_from')) {
            $query->where('start_date', '>=', $request->get('start_date_from'));
        }

        if ($request->filled('start_date_to')) {
            $query->where('start_date', '<=', $request->get('start_date_to'));
        }

        $mentorships = $query->orderBy('created_at', 'desc')
                            ->paginate(20)
                            ->withQueryString();

        // Get mentors for filter dropdown
        // Only users with 'mentor' role can be mentors (not pastors or admins)
        $mentors = User::where('role', 'mentor')
                      ->orderBy('name')
                      ->get();

        return view('mentorships.index', compact('mentorships', 'mentors'));
    }

    /**
     * Show the form for creating a new mentorship.
     */
    public function create(): View
    {
        // Only admins and mentors can create mentorships, not pastors
        if (auth()->user()->isPastor()) {
            abort(403, 'Access denied. Pastors can only view mentorships.');
        }

        $user = auth()->user();
        $members = Member::whereDoesntHave('mentorships', function ($query) {
            $query->where('status', 'active');
        })->orderBy('full_name')->get();

        // For mentors: auto-set mentor_id to current user, don't show mentor selection
        // For admins: show mentor selection dropdown
        $mentors = null;
        if ($user->isAdmin()) {
            // Only users with 'mentor' role can be mentors (not pastors or admins)
            $mentors = User::where('role', 'mentor')
                          ->orderBy('name')
                          ->get();
        }

        return view('mentorships.create', compact('members', 'mentors'));
    }

    /**
     * Store a newly created mentorship.
     */
    public function store(MentorshipRequest $request)
    {
        // Only admins and mentors can create mentorships, not pastors
        if (auth()->user()->isPastor()) {
            abort(403, 'Access denied. Pastors can only view mentorships.');
        }

        $user = auth()->user();
        $validated = $request->validated();

        // For mentors: auto-set mentor_id to current user
        if ($user->isMentor()) {
            $validated['mentor_id'] = $user->id;
        }

        $mentorship = Mentorship::create($validated);

        return redirect()
            ->route('mentorships.show', $mentorship)
            ->with('success', 'Mentorship relationship created successfully.');
    }

    /**
     * Display the specified mentorship.
     */
    public function show(Mentorship $mentorship): View
    {
        $mentorship->load(['member', 'mentor']);

        // Get mentorship statistics
        $endDate = $mentorship->end_date ?? now();
        $stats = [
            'duration_days' => (int) $mentorship->start_date->diffInDays($endDate), // Ensure whole number
            'meetings_held' => 0, // This would be calculated from meeting records
            'last_meeting' => null, // This would come from meeting records
        ];

        return view('mentorships.show', compact('mentorship', 'stats'));
    }

    /**
     * Show the form for editing the specified mentorship.
     */
    public function edit(Mentorship $mentorship): View
    {
        // Only admins and mentors can edit mentorships, not pastors
        if (auth()->user()->isPastor()) {
            abort(403, 'Access denied. Pastors can only view mentorships.');
        }

        $members = Member::orderBy('full_name')->get();
        // Only users with 'mentor' role can be mentors (not pastors or admins)
        $mentors = User::where('role', 'mentor')
                      ->orderBy('name')
                      ->get();

        return view('mentorships.edit', compact('mentorship', 'members', 'mentors'));
    }

    /**
     * Update the specified mentorship.
     */
    public function update(MentorshipRequest $request, Mentorship $mentorship)
    {
        // Only admins and mentors can update mentorships, not pastors
        if (auth()->user()->isPastor()) {
            abort(403, 'Access denied. Pastors can only view mentorships.');
        }

        $mentorship->update($request->validated());

        return redirect()
            ->route('mentorships.show', $mentorship)
            ->with('success', 'Mentorship relationship updated successfully.');
    }

    /**
     * Remove the specified mentorship.
     */
    public function destroy(Mentorship $mentorship)
    {
        // Only admins and mentors can delete mentorships, not pastors
        if (auth()->user()->isPastor()) {
            abort(403, 'Access denied. Pastors can only view mentorships.');
        }

        $mentorship->delete();

        return redirect()
            ->route('mentorships.index')
            ->with('success', 'Mentorship relationship deleted successfully.');
    }

    /**
     * Get mentorships for a specific member.
     */
    public function memberMentorships(Member $member): View
    {
        $mentorships = $member->mentorships()
            ->with('mentor')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mentorships.member', compact('member', 'mentorships'));
    }

    /**
     * Get mentorships for a specific mentor.
     */
    public function mentorMentorships(User $mentor): View
    {
        $mentorships = $mentor->mentorships()
            ->with('member')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mentorships.mentor', compact('mentor', 'mentorships'));
    }

    /**
     * Update mentorship status.
     */
    public function updateStatus(Request $request, Mentorship $mentorship)
    {
        // Only admins and mentors can update mentorship status, not pastors
        if (auth()->user()->isPastor()) {
            abort(403, 'Access denied. Pastors can only view mentorships.');
        }

        $request->validate([
            'status' => ['required', 'in:active,completed,paused'],
        ]);

        $mentorship->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Mentorship status updated successfully.');
    }

    /**
     * Get mentorship statistics.
     */
    public function statistics(): View
    {
        $stats = [
            'total_mentorships' => Mentorship::count(),
            'active_mentorships' => Mentorship::where('status', 'active')->count(),
            'completed_mentorships' => Mentorship::where('status', 'completed')->count(),
            'paused_mentorships' => Mentorship::where('status', 'paused')->count(),
        ];

        // Get mentors with most mentees (only users with 'mentor' role)
        $topMentors = User::where('role', 'mentor')
            ->withCount(['mentorships' => function ($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('mentorships_count', 'desc')
            ->take(10)
            ->get();

        // Get recent mentorships
        $recentMentorships = Mentorship::with(['member', 'mentor'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('mentorships.statistics', compact('stats', 'topMentors', 'recentMentorships'));
    }

    /**
     * Export mentorships to CSV.
     */
    public function export(Request $request)
    {
        $query = Mentorship::with(['member', 'mentor']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('member', function ($memberQuery) use ($search) {
                    $memberQuery->where('full_name', 'like', "%{$search}%")
                               ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('mentor', function ($mentorQuery) use ($search) {
                    $mentorQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('mentor_id')) {
            $query->where('mentor_id', $request->get('mentor_id'));
        }

        $mentorships = $query->orderBy('created_at', 'desc')->get();

        $csvContent = $this->generateCsvContent($mentorships);

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="mentorships_export_' . date('Y-m-d') . '.csv"');
    }

    /**
     * Generate CSV content for export.
     */
    private function generateCsvContent($mentorships): string
    {
        $headers = ['ID', 'Member Name', 'Member Phone', 'Mentor Name', 'Start Date', 'Status', 'Meeting Frequency', 'Notes', 'Created At'];
        $csv = implode(',', $headers) . "\n";

        foreach ($mentorships as $mentorship) {
            $row = [
                $mentorship->id,
                '"' . str_replace('"', '""', $mentorship->member->full_name) . '"',
                $mentorship->member->phone,
                '"' . str_replace('"', '""', $mentorship->mentor->name) . '"',
                $mentorship->start_date->format('Y-m-d'),
                $mentorship->status,
                $mentorship->meeting_frequency ?? '',
                '"' . str_replace('"', '""', $mentorship->notes ?? '') . '"',
                $mentorship->created_at->format('Y-m-d H:i:s'),
            ];
            $csv .= implode(',', $row) . "\n";
        }

        return $csv;
    }
}
