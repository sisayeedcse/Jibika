<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityProfile extends Model
{
    protected $fillable = ['user_id','full_name','location','verified'];
    protected $casts = ['verified'=>'boolean'];
    public function user() { return $this->belongsTo(User::class); }
}
