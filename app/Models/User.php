<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use HasApiTokens;

    /**
     * User roles
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_PASTOR = 'pastor';
    public const ROLE_MENTOR = 'mentor';
    public const ROLE_MEMBER = 'member';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'avatar',
        'google_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Check if user is pastor
     */
    public function isPastor(): bool
    {
        return $this->hasRole(self::ROLE_PASTOR);
    }

    /**
     * Check if user is mentor
     */
    public function isMentor(): bool
    {
        return $this->hasRole(self::ROLE_MENTOR);
    }


    /**
     * Check if user is member
     */
    public function isMember(): bool
    {
        return $this->hasRole(self::ROLE_MEMBER);
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Check if user can manage other users (admin only)
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can manage classes (admin, pastor, or mentor)
     */
    public function canManageClasses(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_PASTOR, self::ROLE_MENTOR]);
    }

    /**
     * Check if user can manage members (admin, pastor, or mentor)
     */
    public function canManageMembers(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_PASTOR, self::ROLE_MENTOR]);
    }

    /**
     * Get all available roles
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_PASTOR,
            self::ROLE_MENTOR,
            self::ROLE_MEMBER,
        ];
    }

    /**
     * Get all available roles with labels
     */
    public static function getRolesWithLabels(): array
    {
        return [
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_PASTOR => 'Pastor',
            self::ROLE_MENTOR => 'Mentor',
            self::ROLE_MEMBER => 'Member',
        ];
    }

    /**
     * Get members associated with this user
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Get classes mentored by this user
     */
    public function mentoredClasses(): HasMany
    {
        return $this->hasMany(DiscipleshipClass::class, 'mentor_id');
    }

    /**
     * Get mentorships where this user is the mentor
     */
    public function mentorships(): HasMany
    {
        return $this->hasMany(Mentorship::class, 'mentor_id');
    }

    /**
     * Get bookings where this user is the mentor
     */
    public function bookingsAsMentor(): HasMany
    {
        return $this->hasMany(Booking::class, 'mentor_id');
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }

        if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return $this->avatar;
        }

        return \Illuminate\Support\Facades\Storage::url($this->avatar);
    }
}
