<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['user_id','action','model_type','model_id','previous_state','new_state','metadata_json'];
    protected $casts = [
        'model_id'=>'integer',
        'previous_state'=>'array',
        'new_state'=>'array',
        'metadata_json'=>'array',
    ];
    public function user() { return $this->belongsTo(User::class); }

    protected static function booted()
    {
        static::creating(function (AuditLog $log) {
            if (is_array($log->previous_state)) {
                $log->previous_state = static::sanitizeState($log->previous_state);
            }
            if (is_array($log->new_state)) {
                $log->new_state = static::sanitizeState($log->new_state);
            }
            if (is_array($log->metadata_json)) {
                $log->metadata_json = static::sanitizeState($log->metadata_json);
            }
        });

        static::updating(function ($auditLog) {
            throw new \RuntimeException('Audit logs are append-only and cannot be modified.');
        });

        static::deleting(function ($auditLog) {
            throw new \RuntimeException('Audit logs are append-only and cannot be deleted.');
        });
    }

    public static function sanitizeState(?array $state): ?array
    {
        if ($state === null) return null;
        $sensitiveKeys = ['password', 'password_confirmation', 'remember_token', 'token', 'secret', 'api_key', 'private_key'];
        foreach ($sensitiveKeys as $key) {
            if (array_key_exists($key, $state)) {
                $state[$key] = '[REDACTED]';
            }
        }
        return $state;
    }
}
