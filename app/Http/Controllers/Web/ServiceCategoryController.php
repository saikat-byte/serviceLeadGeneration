<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function show($slug)
    {
        // Category fetch (Assuming slug or id is passed, using slug convention)
        $category = ServiceCategory::where('slug', $slug)
            ->orWhere('id', $slug)
            ->where('is_active', true)
            ->with(['children' => function($q) {
                $q->where('is_active', true);
            }, 'parent'])
            ->firstOrFail();

        // Fetch services under this category
        $services = $category->services()
            ->where('is_active', true)
            ->paginate(12);

        return view('services.category', compact('category', 'services'));
    }
}