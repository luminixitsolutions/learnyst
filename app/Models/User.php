<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Services\PermissionService;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'role_id', 'created_by', 'name', 'email', 'google_id', 'phone', 'address', 'notes', 'avatar', 'bio', 'expertise',
        'social_links', 'password', 'total_spent', 'is_active', 'last_login_at', 'email_verified_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'total_spent' => 'decimal:2',
            'is_active' => 'boolean',
            'social_links' => 'array',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function creator()
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'owner_user_id');
    }

    public function createdUsers()
    {
        return $this->hasMany(self::class, 'created_by');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function subAdminScopes()
    {
        return $this->hasMany(SubAdminScope::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->slug === 'super-admin';
    }

    public function isCompanyStaff(): bool
    {
        return in_array($this->role?->slug, ['admin', 'sub-admin', 'super-admin', 'counselor'], true);
    }

    public function isSubAdmin(): bool
    {
        return $this->role?->slug === 'sub-admin';
    }

    public function isStaff(): bool
    {
        return in_array($this->role?->slug, ['admin', 'sub-admin'], true);
    }

    public function isInstructor(): bool
    {
        return $this->role?->slug === 'instructor';
    }

    public function isLearner(): bool
    {
        return $this->role?->slug === 'learner';
    }

    public function isAlumni(): bool
    {
        return $this->role?->slug === 'alumni';
    }

    public function isParent(): bool
    {
        return $this->role?->slug === 'parent';
    }

    public function isCounselor(): bool
    {
        return $this->role?->slug === 'counselor';
    }

    public function isStudentPanelUser(): bool
    {
        return in_array($this->role?->slug, ['learner', 'alumni', 'parent'], true);
    }

    public function hasRole(string $slug): bool
    {
        return $this->role?->slug === $slug;
    }

    public function hasPermission(string $module, string $action = 'view'): bool
    {
        return PermissionService::hasPermission($this, $module, $action);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_instructors');
    }

    public function batches()
    {
        return $this->belongsToMany(Batch::class, 'batch_learners')->withPivot('status', 'progress');
    }

    public function segments()
    {
        return $this->belongsToMany(Segment::class, 'segment_user');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function notifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }
}
