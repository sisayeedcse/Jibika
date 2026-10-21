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
        'role' => 'worker',
        'status' => 'active',
    ];

    protected $fillable = [
        'name','email','password','role','status',
    ];

    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function workerProfile() { return $this->hasOne(WorkerProfile::class); }
    public function communityProfile() { return $this->hasOne(CommunityProfile::class); }
    public function vendorProfile() { return $this->hasOne(VendorProfile::class); }

    public function assetRequests() { return $this->hasMany(AssetRequest::class, 'worker_id'); }

    public function isWorker(): bool { return $this->role === 'worker'; }
    public function isCommunity(): bool { return $this->role === 'community'; }
    public function isContributor(): bool { return $this->role === 'contributor'; }
    public function isVendor(): bool { return $this->role === 'vendor'; }
    public function isAdmin(): bool { return $this->role === 'admin'; }

    public function isActive(): bool { return $this->status === 'active'; }
}
