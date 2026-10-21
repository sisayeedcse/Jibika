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
}
