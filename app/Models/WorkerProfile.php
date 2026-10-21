<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerProfile extends Model
{
    protected $fillable = ['user_id','occupation','experience_years','location','bio','verified'];
    public function user() { return $this->belongsTo(User::class); }
}
