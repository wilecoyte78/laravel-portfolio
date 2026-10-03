<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class PublicPageController extends Controller
{
    public function home(): Response
    {
        $page = Page::where('slug', 'home')
            ->where('is_published', true)
            ->first();

        return Inertia::render('Public/Home', [
            'page' => $page,
        ]);
    }

    public function show(string $slug): Response
    {
        $page = Page::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return Inertia::render('Public/Page', [
            'page' => $page,
        ]);
    }
}
