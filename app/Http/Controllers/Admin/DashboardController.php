<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleToken;
use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_pages' => Page::count(),
                'published_pages' => Page::where('is_published', true)->count(),
            ],
            'gmailConnected' => GoogleToken::query()->exists(),
        ]);
    }
}
