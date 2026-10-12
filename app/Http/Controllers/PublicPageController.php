<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Support\Facades\File;
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

    public function resume(): Response
    {
        $page = Page::where('slug', 'resume')->first();

        $path = config('resume.public_path')
            .DIRECTORY_SEPARATOR
            .config('resume.filename', 'resume.pdf');

        $resume = null;

        if (File::exists($path)) {
            $resume = [
                'url' => '/files/'.config('resume.filename', 'resume.pdf'),
                'modified_at' => File::lastModified($path),
            ];
        }

        return Inertia::render('Public/Resume', [
            'page' => $page,
            'resume' => $resume,
        ]);
    }

    public function projects(): Response
    {
        return Inertia::render('Public/Projects');
    }
}
