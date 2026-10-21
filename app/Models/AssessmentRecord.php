<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentRecord extends Model
{
    protected $fillable = [
        'request_id','suitability_category','numerical_score','explanation_json',
        'method_version','is_ai_generated','provider_name',
    ];
    protected $casts = [
        'explanation_json' => 'array',
        'numerical_score' => 'decimal:2',
        'is_ai_generated' => 'boolean',
    ];
    public function request() { return $this->belongsTo(AssetRequest::class, 'request_id'); }
}
