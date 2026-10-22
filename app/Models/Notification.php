<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id','title','title_bn','body_en','body_bn','type','read_at'];
    protected $casts = ['read_at'=>'datetime'];

    protected static function booted()
    {
        static::creating(function ($notification) {
            if (empty($notification->title_bn)) {
                $notification->title_bn = $notification->title ?? '';
            }
            if (empty($notification->title)) {
                $notification->title = $notification->title_bn ?? '';
            }
            if (empty($notification->body_en)) {
                $notification->body_en = $notification->body_bn ?? '';
            }
            if (empty($notification->body_bn)) {
                $notification->body_bn = $notification->body_en ?? '';
            }
        });
    }

    public function user() { return $this->belongsTo(User::class); }
}
