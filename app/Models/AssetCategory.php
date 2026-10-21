<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    protected $fillable = ['name','name_bn','estimated_cost_bdt','description'];
    protected $casts = ['estimated_cost_bdt'=>'decimal:2'];
}
