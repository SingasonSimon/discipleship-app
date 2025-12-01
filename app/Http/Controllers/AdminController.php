<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\DiscipleshipClass;
use App\Models\Member;
use App\Models\Mentorship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (! auth()->user() || ! auth()->user()->isAdmin()) {
                abort(403, 'Access denied. Admin privileges required.');
            }

            return $next($request);
        });
    }

    /**
     * Show admin dashboard
     */
    public function index()
    {
        $stats = $this->getSystemStats();
        $recentActivity = $this->getRecentActivity();

        return view('admin.dashboard', compact('stats', 'recentActivity'));
    }

    /**
     * Show user management page
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        // Filter by email verification status
        if ($request->filled('email_verified')) {
            if ($request->get('email_verified') === 'verified') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $perPage = $request->get('per_page', 20);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $roles = User::getRoles();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show create user form
     */
    public function createUser(): \Illuminate\View\View
    {
        $this->authorize('create', User::class);

        $roles = [
            User::ROLE_PASTOR => 'Pastor',
            User::ROLE_MENTOR => 'Mentor',
        ];

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created pastor or mentor account
     */
    public function storeUser(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:25', 'unique:users,phone'],
            'role' => ['required', Rule::in([User::ROLE_PASTOR, User::ROLE_MENTOR])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password' => $validated['password'],
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Account created successfully. Share the credentials below with the new user.')
            ->with('new_user_credentials', [
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);
    }

    /**
     * Show user details
     */
    public function showUser(User $user)
    {
        $user->load(['members', 'mentoredClasses', 'mentorships']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Manually verify a user's email
     */
    public function verifyUser(User $user)
    {
        if ($user->email_verified_at) {
            return redirect()
                ->route('admin.users')
                ->with('error', 'User email is already verified.');
        }

        $user->forceFill(['email_verified_at' => now()])->save();

        return redirect()
            ->route('admin.users')
            ->with('success', "Email for {$user->name} has been verified successfully.");
    }

    /**
     * Resend verification email to user
     */
    public function resendVerification(User $user)
    {
        if ($user->email_verified_at) {
            return redirect()
                ->route('admin.users')
                ->with('error', 'User email is already verified.');
        }

        // Send verification notification
        $user->sendEmailVerificationNotification();

        return redirect()
            ->route('admin.users')
            ->with('success', "Verification email has been sent to {$user->email}.");
    }

    /**
     * Get system statistics
     */
    private function getSystemStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_members' => Member::count(),
            'total_classes' => DiscipleshipClass::count(),
            'total_sessions' => ClassSession::count(),
            'total_attendance' => Attendance::count(),
            'total_mentorships' => Mentorship::count(),
            'active_classes' => DiscipleshipClass::where('is_active', true)->count(),
            'pending_email_verifications' => User::whereNull('email_verified_at')->count(),
        ];
    }

    /**
     * Get recent activity
     */
    private function getRecentActivity(): array
    {
        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();
        $recentMembers = Member::orderBy('created_at', 'desc')->limit(5)->get();
        $recentClasses = DiscipleshipClass::orderBy('created_at', 'desc')->limit(5)->get();

        return [
            'recent_users' => $recentUsers,
            'recent_members' => $recentMembers,
            'recent_classes' => $recentClasses,
        ];
    }

}
