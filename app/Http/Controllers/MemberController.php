<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\Member;
use App\Models\User;
use App\Services\MemberImportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(
        private MemberImportService $importService
    ) {
        $this->middleware('auth');
        $this->authorizeResource(Member::class, 'member');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Member::query();

        // For mentors: only show members they're linked to via mentorships
        if ($user->isMentor()) {
            $query->whereHas('mentorships', function ($q) use ($user) {
                $q->where('mentor_id', $user->id);
            });
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by conversion date
        if ($request->filled('conversion_date_from')) {
            $query->where('date_of_conversion', '>=', $request->get('conversion_date_from'));
        }

        if ($request->filled('conversion_date_to')) {
            $query->where('date_of_conversion', '<=', $request->get('conversion_date_to'));
        }

        // Filter by preferred contact
        if ($request->filled('preferred_contact')) {
            $query->where('preferred_contact', $request->get('preferred_contact'));
        }

        $members = $query->orderBy('created_at', 'desc')
                        ->paginate(20)
                        ->withQueryString();

        return view('members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('members.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MemberRequest $request)
    {
        $validated = $request->validated();
        
        // Create User account for the member with default password
        $defaultPassword = env('DEFAULT_SHARED_PASSWORD', 'password');
        
        $user = User::create([
            'name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => User::ROLE_MEMBER,
            'password' => Hash::make($defaultPassword),
            'email_verified_at' => now(), // Auto-verify email for admin/mentor created accounts
        ]);

        // Create Member profile linked to the User account
        $member = Member::create([
            'user_id' => $user->id,
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'date_of_conversion' => $validated['date_of_conversion'],
            'preferred_contact' => $validated['preferred_contact'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('members.index')
            ->with('success', 'Member created successfully. They can log in with email: ' . $validated['email'] . ' and password: ' . $defaultPassword)
            ->with('new_member_credentials', [
                'email' => $validated['email'],
                'password' => $defaultPassword,
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member): View
    {
        $member->load([
            'attendance.classSession.class',
            'mentorships.mentor',
        ]);

        // Get attendance statistics
        $attendanceStats = [
            'total_sessions' => $member->attendance()->count(),
            'present_count' => $member->attendance()->where('status', 'present')->count(),
            'absent_count' => $member->attendance()->where('status', 'absent')->count(),
            'excused_count' => $member->attendance()->where('status', 'excused')->count(),
        ];

        // Calculate attendance rate
        $attendanceStats['attendance_rate'] = $attendanceStats['total_sessions'] > 0
            ? round(($attendanceStats['present_count'] / $attendanceStats['total_sessions']) * 100, 2)
            : 0;

        return view('members.show', compact('member', 'attendanceStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member): View
    {
        return view('members.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MemberRequest $request, Member $member)
    {
        $validated = $request->validated();
        
        // Update member
        $member->update($validated);
        
        // If member has a linked user account, update it as well
        if ($member->user_id && $member->user) {
            $member->user->update([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ]);
        }

        return redirect()
            ->route('members.show', $member)
            ->with('success', 'Member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()
            ->route('members.index')
            ->with('success', 'Member deleted successfully.');
    }

    /**
     * Show the import form
     */
    public function import(): View
    {
        $this->authorize('import', Member::class);
        return view('members.import');
    }

    /**
     * Process CSV import
     */
    public function processImport(Request $request)
    {
        $this->authorize('import', Member::class);
        
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        try {
            $results = $this->importService->importFromCsv(
                $request->file('csv_file'),
                $request->user()
            );

            $message = "Import completed. {$results['success']} members imported successfully.";

            if ($results['failed'] > 0) {
                $message .= " {$results['failed']} rows failed to import.";
            }

            return redirect()
                ->route('members.index')
                ->with('success', $message)
                ->with('import_results', $results);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate(): Response
    {
        $this->authorize('import', Member::class);
        
        $csvContent = $this->importService->getCsvTemplate();

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="members_template.csv"');
    }

    /**
     * Export members to CSV
     */
    public function export(Request $request): Response
    {
        $this->authorize('viewAny', Member::class);
        
        $query = Member::query();

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('conversion_date_from')) {
            $query->where('date_of_conversion', '>=', $request->get('conversion_date_from'));
        }

        if ($request->filled('conversion_date_to')) {
            $query->where('date_of_conversion', '<=', $request->get('conversion_date_to'));
        }

        if ($request->filled('preferred_contact')) {
            $query->where('preferred_contact', $request->get('preferred_contact'));
        }

        $members = $query->orderBy('created_at', 'desc')->get();

        $csvContent = $this->generateCsvContent($members);

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="members_export_' . date('Y-m-d') . '.csv"');
    }

    /**
     * Generate CSV content for export
     */
    private function generateCsvContent($members): string
    {
        $headers = ['ID', 'Full Name', 'Phone', 'Email', 'Conversion Date', 'Preferred Contact', 'Notes', 'Created At'];
        $csv = implode(',', $headers) . "\n";

        foreach ($members as $member) {
            $row = [
                $member->id,
                '"' . str_replace('"', '""', $member->full_name) . '"',
                $member->phone,
                $member->email ?? '',
                $member->date_of_conversion,
                $member->preferred_contact,
                '"' . str_replace('"', '""', $member->notes ?? '') . '"',
                $member->created_at->format('Y-m-d H:i:s'),
            ];
            $csv .= implode(',', $row) . "\n";
        }

        return $csv;
    }
}
