<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\MentorshipController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API v1 routes
Route::prefix('v1')->group(function () {

    // Authentication routes (public)
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    // Protected API routes
    Route::middleware('auth:sanctum')->group(function () {

        // Authentication routes (protected)
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
        Route::get('/auth/permissions', [AuthController::class, 'permissions']);

        // Dashboard
        Route::get('/dashboard/summary', [DashboardController::class, 'summary']);

        // Messages
        Route::apiResource('messages', \App\Http\Controllers\Api\MessageController::class)->names([
            'index' => 'api.messages.index',
            'store' => 'api.messages.store',
            'show' => 'api.messages.show',
            'update' => 'api.messages.update',
            'destroy' => 'api.messages.destroy',
        ]);
        Route::post('/messages/{message}/send-now', [\App\Http\Controllers\Api\MessageController::class, 'sendNow'])->name('api.messages.sendNow');

        // Members
        Route::apiResource('members', MemberController::class)->names([
            'index' => 'api.members.index',
            'store' => 'api.members.store',
            'show' => 'api.members.show',
            'update' => 'api.members.update',
            'destroy' => 'api.members.destroy',
        ]);
        Route::get('/members/{member}/attendance', [MemberController::class, 'attendance']);
        Route::get('/members/{member}/mentorships', [MemberController::class, 'mentorships']);
        Route::get('/members/{member}/statistics', [MemberController::class, 'statistics']);

        // Classes
        Route::apiResource('classes', ClassController::class)->names([
            'index' => 'api.classes.index',
            'store' => 'api.classes.store',
            'show' => 'api.classes.show',
            'update' => 'api.classes.update',
            'destroy' => 'api.classes.destroy',
        ]);
        Route::patch('/classes/{class}/toggle-status', [ClassController::class, 'toggleStatus'])->name('api.classes.toggleStatus');
        Route::get('/classes/{class}/sessions', [ClassController::class, 'sessions'])->name('api.classes.sessions');
        Route::get('/classes/{class}/statistics', [ClassController::class, 'statistics'])->name('api.classes.statistics');
        Route::get('/classes/mentors', [ClassController::class, 'mentors'])->name('api.classes.mentors');

        // Sessions
        Route::apiResource('classes.sessions', SessionController::class)->shallow()->names([
            'index' => 'api.classes.sessions.index',
            'store' => 'api.classes.sessions.store',
            'show' => 'api.classes.sessions.show',
            'update' => 'api.classes.sessions.update',
            'destroy' => 'api.classes.sessions.destroy',
        ]);
        Route::get('/sessions/{session}/attendance', [SessionController::class, 'attendance']);
        Route::get('/sessions/{session}/upcoming', [SessionController::class, 'upcoming']);
        Route::get('/sessions/{session}/statistics', [SessionController::class, 'statistics']);

        // Attendance
        Route::apiResource('attendance', AttendanceController::class)->except(['index'])->names([
            'store' => 'api.attendance.store',
            'show' => 'api.attendance.show',
            'update' => 'api.attendance.update',
            'destroy' => 'api.attendance.destroy',
        ]);
        Route::post('/attendance/bulk', [AttendanceController::class, 'storeBulk'])->name('api.attendance.storeBulk');
        Route::get('/attendance/member/{member}/stats', [AttendanceController::class, 'memberStats'])->name('api.attendance.memberStats');
        Route::get('/attendance/class/{class}/stats', [AttendanceController::class, 'classStats'])->name('api.attendance.classStats');
        Route::get('/attendance/session/{session}', [AttendanceController::class, 'sessionAttendance'])->name('api.attendance.sessionAttendance');

        // Mentorships
        Route::apiResource('mentorships', MentorshipController::class)->names([
            'index' => 'api.mentorships.index',
            'store' => 'api.mentorships.store',
            'show' => 'api.mentorships.show',
            'update' => 'api.mentorships.update',
            'destroy' => 'api.mentorships.destroy',
        ]);
        Route::get('/members/{member}/mentorships', [MentorshipController::class, 'memberMentorships'])->name('api.mentorships.member');
        Route::get('/mentors/{mentor}/mentorships', [MentorshipController::class, 'mentorMentorships'])->name('api.mentorships.mentor');
        Route::patch('/mentorships/{mentorship}/status', [MentorshipController::class, 'updateStatus'])->name('api.mentorships.updateStatus');
        Route::get('/mentorships/statistics', [MentorshipController::class, 'statistics'])->name('api.mentorships.statistics');
        Route::get('/mentorships/mentors', [MentorshipController::class, 'mentors'])->name('api.mentorships.mentors');
        Route::get('/mentorships/available-members', [MentorshipController::class, 'availableMembers'])->name('api.mentorships.availableMembers');

        // Reports
        Route::get('/reports/attendance-trends', [\App\Http\Controllers\Api\ReportController::class, 'attendanceTrends'])->name('api.reports.attendanceTrends');
        Route::get('/reports/member-engagement', [\App\Http\Controllers\Api\ReportController::class, 'memberEngagement'])->name('api.reports.memberEngagement');
        Route::get('/reports/class-performance', [\App\Http\Controllers\Api\ReportController::class, 'classPerformance'])->name('api.reports.classPerformance');
        Route::get('/reports/mentorship-success', [\App\Http\Controllers\Api\ReportController::class, 'mentorshipSuccess'])->name('api.reports.mentorshipSuccess');

    });

});
