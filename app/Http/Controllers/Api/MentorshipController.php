<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MentorshipRequest;
use App\Models\Member;
use App\Models\Mentorship;
use App\Models\User;
use Illuminate\Http\Request;

class MentorshipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(function ($request, $next) {
            if (! $request->user()->hasAnyRole(['admin', 'pastor', 'mentor'])) {
                abort(403, 'Access denied. Only admins, pastors, or mentors can manage mentorships.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of mentorships
     */
    public function index(Request $request)
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

        $perPage = $request->get('per_page', 20);
        $mentorships = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $mentorships,
        ]);
    }

    /**
     * Store a newly created mentorship
     */
    public function store(MentorshipRequest $request)
    {
        $mentorship = Mentorship::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Mentorship relationship created successfully',
            'data' => $mentorship->load(['member', 'mentor']),
        ], 201);
    }

    /**
     * Display the specified mentorship
     */
    public function show(Mentorship $mentorship)
    {
        $mentorship->load(['member', 'mentor']);

        // Get mentorship statistics
        $stats = [
            'duration_days' => $mentorship->start_date->diffInDays(now()),
            'meetings_held' => 0, // This would be calculated from meeting records
            'last_meeting' => null, // This would come from meeting records
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'mentorship' => $mentorship,
                'statistics' => $stats,
            ],
        ]);
    }

    /**
     * Update the specified mentorship
     */
    public function update(MentorshipRequest $request, Mentorship $mentorship)
    {
        $mentorship->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Mentorship relationship updated successfully',
            'data' => $mentorship->load(['member', 'mentor']),
        ]);
    }

    /**
     * Remove the specified mentorship
     */
    public function destroy(Mentorship $mentorship)
    {
        $mentorship->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mentorship relationship deleted successfully',
        ]);
    }

    /**
     * Get mentorships for a specific member
     */
    public function memberMentorships(Member $member)
    {
        $mentorships = $member->mentorships()
            ->with('mentor')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $mentorships,
        ]);
    }

    /**
     * Get mentorships for a specific mentor
     */
    public function mentorMentorships(User $mentor)
    {
        $mentorships = $mentor->mentorships()
            ->with('member')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $mentorships,
        ]);
    }

    /**
     * Update mentorship status
     */
    public function updateStatus(Request $request, Mentorship $mentorship)
    {
        $request->validate([
            'status' => ['required', 'in:active,completed,paused'],
        ]);

        $mentorship->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mentorship status updated successfully',
            'data' => $mentorship,
        ]);
    }

    /**
     * Get mentorship statistics
     */
    public function statistics()
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

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $stats,
                'top_mentors' => $topMentors,
                'recent_mentorships' => $recentMentorships,
            ],
        ]);
    }

    /**
     * Get available mentors
     */
    public function mentors()
    {
        // Only users with 'mentor' role can be mentors (not pastors or admins)
        $mentors = User::where('role', 'mentor')
                      ->orderBy('name')
                      ->get(['id', 'name', 'role']);

        return response()->json([
            'success' => true,
            'data' => $mentors,
        ]);
    }

    /**
     * Get available members (those without active mentorships)
     */
    public function availableMembers()
    {
        $members = Member::whereDoesntHave('mentorships', function ($query) {
            $query->where('status', 'active');
        })->orderBy('full_name')->get(['id', 'full_name', 'phone']);

        return response()->json([
            'success' => true,
            'data' => $members,
        ]);
    }
}
