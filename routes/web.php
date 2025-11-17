<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MentorshipController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard routes
// Note: 'verified' middleware removed since emails are auto-verified on registration
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Members routes
    Route::get('/members/import', [MemberController::class, 'import'])->name('members.import');
    Route::post('/members/import', [MemberController::class, 'processImport'])->name('members.processImport');
    Route::get('/members/template/download', [MemberController::class, 'downloadTemplate'])->name('members.downloadTemplate');
    Route::get('/members/export', [MemberController::class, 'export'])->name('members.export');
    Route::resource('members', MemberController::class);

    // Member-only routes (enrollments, bookings, testimonials) - MUST be before classes routes to avoid route conflicts
    Route::prefix('member')->name('member.')->group(function () {
        // Enrollments
        Route::get('/enrollments', [\App\Http\Controllers\Member\EnrollmentController::class, 'index'])->name('enrollments.index');
        Route::get('/classes/{class}/enroll', [\App\Http\Controllers\Member\EnrollmentController::class, 'create'])->name('enrollments.create');
        Route::post('/classes/{class}/enroll', [\App\Http\Controllers\Member\EnrollmentController::class, 'store'])->name('enrollments.store');
        Route::get('/enrollments/{enrollment}', [\App\Http\Controllers\Member\EnrollmentController::class, 'show'])->name('enrollments.show');
        Route::delete('/enrollments/{enrollment}', [\App\Http\Controllers\Member\EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    });

    // Classes routes
    Route::resource('classes', ClassController::class);
    Route::patch('/classes/{class}/toggle-status', [ClassController::class, 'toggleStatus'])->name('classes.toggleStatus');
    Route::get('/classes/{class}/schedule', [ClassController::class, 'schedule'])->name('classes.schedule');

    // Class Content routes
    Route::resource('classes.content', \App\Http\Controllers\ClassContentController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::patch('/classes/{class}/content/{content}/toggle-publish', [\App\Http\Controllers\ClassContentController::class, 'togglePublish'])->name('classes.content.togglePublish');
    Route::post('/classes/{class}/content/{content}/progress', [\App\Http\Controllers\ClassContentController::class, 'updateProgress'])->name('classes.content.progress');

    // Sessions routes
    Route::resource('classes.sessions', SessionController::class)->shallow();
    Route::get('/sessions/{session}/attendance', [SessionController::class, 'attendance'])->name('sessions.attendance');
    Route::post('/sessions/{session}/send-google-meet-link', [SessionController::class, 'sendGoogleMeetLink'])->name('sessions.sendGoogleMeetLink');
    Route::get('/sessions/{session}/upcoming', [SessionController::class, 'upcoming'])->name('sessions.upcoming');
    Route::get('/sessions/{session}/statistics', [SessionController::class, 'statistics'])->name('sessions.statistics');

    // Attendance routes
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
    Route::post('/attendance/bulk', [AttendanceController::class, 'storeBulk'])->name('attendance.storeBulk');
    Route::get('/attendance/member/{member}/stats', [AttendanceController::class, 'memberStats'])->name('attendance.memberStats');
    Route::get('/attendance/class/{class}/stats', [AttendanceController::class, 'classStats'])->name('attendance.classStats');
    Route::get('/attendance/session/{session}/export', [AttendanceController::class, 'exportSession'])->name('attendance.exportSession');

    // Messages routes (must be before mentorships to avoid route conflicts)
    Route::resource('messages', \App\Http\Controllers\MessageController::class);
    Route::post('/messages/{message}/send-now', [\App\Http\Controllers\MessageController::class, 'sendNow'])->name('messages.sendNow');
    Route::post('/messages/send-scheduled', [\App\Http\Controllers\MessageController::class, 'sendScheduled'])->name('messages.sendScheduled');

    // Mentorships routes
    Route::get('/mentorships/statistics', [MentorshipController::class, 'statistics'])->name('mentorships.statistics');
    Route::get('/mentorships/export', [MentorshipController::class, 'export'])->name('mentorships.export');
    Route::resource('mentorships', MentorshipController::class);
    Route::get('/members/{member}/mentorships', [MentorshipController::class, 'memberMentorships'])->name('mentorships.member');
    Route::get('/mentors/{mentor}/mentorships', [MentorshipController::class, 'mentorMentorships'])->name('mentorships.mentor');
    Route::patch('/mentorships/{mentorship}/status', [MentorshipController::class, 'updateStatus'])->name('mentorships.updateStatus');

    // Reports routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/attendance-trends', [ReportController::class, 'attendanceTrends'])->name('attendance-trends');
        Route::get('/member-engagement', [ReportController::class, 'memberEngagement'])->name('member-engagement');
        Route::get('/class-performance', [ReportController::class, 'classPerformance'])->name('class-performance');
        Route::get('/mentorship-success', [ReportController::class, 'mentorshipSuccess'])->name('mentorship-success');
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
        Route::get('/system-health', [AdminController::class, 'systemHealth'])->name('system-health');
    });
});

require __DIR__.'/auth.php';
