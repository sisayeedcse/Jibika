<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $attributes = [
        'role'   => 'worker',
        'status' => 'active',
    ];

    /**
     * role and status are intentionally NOT in $fillable to prevent mass-assignment of
     * privileged roles. Use explicit setters or direct assignment where authorization
     * has already been checked (e.g., admin user management, registration controller).
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function workerProfile()   { return $this->hasOne(WorkerProfile::class); }
    public function communityProfile(){ return $this->hasOne(CommunityProfile::class); }
    public function vendorProfile()   { return $this->hasOne(VendorProfile::class); }
    public function assetRequests()   { return $this->hasMany(AssetRequest::class, 'worker_id'); }
    public function notifications()   { return $this->hasMany(Notification::class); }
    public function unreadNotifications() {
        return $this->hasMany(Notification::class)->whereNull('read_at');
    }

    // ── Role helpers ─────────────────────────────────────────────────────────

    public function isWorker():      bool { return $this->role === 'worker'; }
    public function isCommunity():   bool { return $this->role === 'community'; }
    public function isContributor(): bool { return $this->role === 'contributor'; }
    public function isVendor():      bool { return $this->role === 'vendor'; }
    public function isAdmin():       bool { return $this->role === 'admin'; }
    public function isActive():      bool { return $this->status === 'active'; }

    /**
     * Safely assign a role. Only allowed values that can be set on registration.
     * 'admin' and 'community' cannot be self-assigned through this method.
     */
    public function assignRegistrationRole(string $role): void
    {
        $allowed = ['worker', 'contributor', 'vendor'];
        $this->role = in_array($role, $allowed, true) ? $role : 'worker';
    }
}
