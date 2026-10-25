<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssetCategoryResource;
use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryApiController extends Controller
{
    public function index(Request $request)
    {
        $categories = AssetCategory::latest()->paginate(20);
        return AssetCategoryResource::collection($categories);
    }

    public function show(AssetCategory $category)
    {
        return new AssetCategoryResource($category);
    }
}
