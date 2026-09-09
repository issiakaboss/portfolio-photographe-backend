<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use Illuminate\Http\Request;

class SiteContentController extends Controller
{
    public function show(Request $request, string $section)
    {
        abort_unless(in_array($section, ['about', 'contact'], true), 404);

        $locale = $request->string('locale')->toString() ?: 'fr';
        $content = SiteContent::query()
            ->where('section', $section)
            ->where('locale', $locale)
            ->first();

        return response()->json(['data' => $content?->content]);
    }
}
