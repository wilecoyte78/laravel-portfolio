<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response;

class ResumeController extends Controller
{
    public function show(): Response
    {
        $page = Page::where('slug', 'resume')->first();

        $path = config('resume.public_path')
            . DIRECTORY_SEPARATOR
            . config('resume.filename', 'resume.pdf');

        $resume = null;

        if (File::exists($path)) {
            $resume = [
                'url' => '/files/' . config('resume.filename', 'resume.pdf'),
                'modified_at' => File::lastModified($path),
            ];
        }

        return Inertia::render('Public/Resume', [
            'page' => $page,
            'resume' => $resume,
        ]);
    }
}