<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscipleshipClass extends Model
{
    use HasFactory;
    /**
     * The table name (if different from default)
     */
    protected $table = 'classes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'mentor_id',
        'created_by',
        'schedule_type',
        'schedule_day',
        'schedule_time',
        'start_date',
        'end_date',
        'capacity',
        'duration_weeks',
        'location',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'schedule_time' => 'string',
        'capacity' => 'integer',
        'duration_weeks' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the mentor (pastor) for this class
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    /**
     * Get the user who created this class
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all sessions for this class
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class, 'class_id');
    }

    /**
     * Get all attendance records for this class
     */
    public function attendance(): HasMany
    {
        return $this->hasManyThrough(Attendance::class, ClassSession::class, 'class_id', 'class_session_id');
    }

    /**
     * Get class enrollments for this class
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(ClassEnrollment::class, 'class_id');
    }

    /**
     * Get all class contents (lessons, assignments, resources, etc.)
     */
    public function contents(): HasMany
    {
        return $this->hasMany(ClassContent::class, 'class_id');
    }

    /**
     * Get published class contents (visible to enrolled members)
     */
    public function publishedContents(): HasMany
    {
        return $this->contents()->where('is_published', true);
    }

    /**
     * Published class contents ordered for sequential learning.
     */
    public function orderedPublishedContents()
    {
        return $this->publishedContents()
            ->orderByRaw('COALESCE(week_number, 0)')
            ->orderBy('order')
            ->orderBy('id');
    }

    /**
     * Get class outline (content type: outline)
     */
    public function outline(): HasMany
    {
        return $this->contents()->where('content_type', 'outline');
    }

    /**
     * Get enrolled members for this class (via enrollments)
     */
    public function enrolledMembers()
    {
        return $this->hasManyThrough(Member::class, ClassEnrollment::class, 'class_id', 'id', 'id', 'member_id');
    }

    /**
     * Get upcoming sessions
     */
    public function upcomingSessions(): HasMany
    {
        return $this->sessions()
            ->where('session_date', '>=', Carbon::today())
            ->orderBy('session_date');
    }

    /**
     * Get past sessions
     */
    public function pastSessions(): HasMany
    {
        return $this->sessions()
            ->where('session_date', '<', Carbon::today())
            ->orderBy('session_date', 'desc');
    }

    /**
     * Get next session
     */
    public function nextSession(): ?ClassSession
    {
        return $this->upcomingSessions()->first();
    }

    /**
     * Get current enrollment count
     */
    public function getEnrollmentCount(): int
    {
        return $this->enrollments()
            ->where('status', 'approved')
            ->count();
    }

    /**
     * Check if class is full
     */
    public function isFull(): bool
    {
        return $this->getEnrollmentCount() >= $this->capacity;
    }

    /**
     * Get available spots
     */
    public function getAvailableSpots(): int
    {
        return max(0, $this->capacity - $this->getEnrollmentCount());
    }

    /**
     * Get overall attendance rate for this class
     */
    public function getAttendanceRate(): float
    {
        $sessions = $this->sessions;
        if ($sessions->isEmpty()) {
            return 0.0;
        }

        $totalAttendance = $this->attendance()->count();
        $totalPossibleAttendance = $sessions->count() * $this->getEnrollmentCount();

        if ($totalPossibleAttendance === 0) {
            return 0.0;
        }

        return round(($totalAttendance / $totalPossibleAttendance) * 100, 2);
    }

    /**
     * Get completion rate (members who completed all sessions)
     */
    public function getCompletionRate(): float
    {
        $totalMembers = $this->getEnrollmentCount();
        if ($totalMembers === 0) {
            return 0.0;
        }

        $completedMembers = $this->enrolledMembers()
            ->whereDoesntHave('attendance', function ($query) {
                $query->whereHas('classSession', function ($q) {
                    $q->where('class_id', $this->id);
                })
                ->where('status', 'absent');
            })
            ->count();

        return round(($completedMembers / $totalMembers) * 100, 2);
    }

    /**
     * Check if class is active (has upcoming sessions)
     */
    public function isActive(): bool
    {
        return $this->upcomingSessions()->exists();
    }

    /**
     * Check if class is completed (all sessions are in the past)
     */
    public function isCompleted(): bool
    {
        return $this->pastSessions()->count() > 0 && ! $this->isActive();
    }

    /**
     * Generate sessions based on schedule rule
     * This is a simplified implementation - in production, you'd use a proper scheduling library
     */
    public function generateSessions(): void
    {
        if (!$this->start_date) {
            return;
        }

        $startDate = Carbon::parse($this->start_date);
        $endDate = $this->end_date ? Carbon::parse($this->end_date) : $startDate->copy()->addWeeks($this->duration_weeks);
        
        // Get the schedule day if specified
        $targetDay = $this->schedule_day ? strtolower($this->schedule_day) : null;
        
        $sessionNumber = 1;
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // If a specific day is set, move to that day of the week
            if ($targetDay) {
                $dayOfWeek = $currentDate->format('l');
                $dayMap = [
                    'monday' => Carbon::MONDAY,
                    'tuesday' => Carbon::TUESDAY,
                    'wednesday' => Carbon::WEDNESDAY,
                    'thursday' => Carbon::THURSDAY,
                    'friday' => Carbon::FRIDAY,
                    'saturday' => Carbon::SATURDAY,
                    'sunday' => Carbon::SUNDAY,
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
            
            if ($this->schedule_type === 'weekly') {
                $shouldCreate = true;
                $nextInterval = 1; // weeks
            } elseif ($this->schedule_type === 'biweekly') {
                $shouldCreate = ($sessionNumber % 2 === 1); // Every other week
                $nextInterval = 2; // weeks
            } elseif ($this->schedule_type === 'monthly') {
                $shouldCreate = true;
                $nextInterval = 1; // months
            } else {
                // Custom - create one session per week for now
                $shouldCreate = true;
                $nextInterval = 1; // weeks
            }

            if ($shouldCreate && $currentDate <= $endDate) {
                $this->sessions()->create([
                    'session_date' => $currentDate->toDateString(),
                    'topic' => "Session {$sessionNumber}",
                    'created_by' => $this->mentor_id,
                ]);
                $sessionNumber++;
            }

            // Move to next session date
            if ($this->schedule_type === 'monthly') {
                $currentDate->addMonth();
            } else {
                $currentDate->addWeeks($nextInterval);
            }
        }
    }

    /**
     * Scope for active classes
     */
    public function scopeActive($query)
    {
        return $query->whereHas('sessions', function ($q) {
            $q->where('session_date', '>=', Carbon::today());
        });
    }

    /**
     * Scope for classes by mentor
     */
    public function scopeByMentor($query, $mentorId)
    {
        return $query->where('mentor_id', $mentorId);
    }
}
