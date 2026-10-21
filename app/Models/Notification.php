<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id','title','title_bn','body_en','body_bn','type','read_at'];
    protected $casts = ['read_at'=>'datetime'];
    public function user() { return $this->belongsTo(User::class); }
}
