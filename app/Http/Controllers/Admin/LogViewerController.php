<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;

class LogViewerController extends Controller
{
    private const MAX_ENTRIES = 500;

private const LEVELS = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];

public function index(Request $request): Response
{
    $viewer = new LaravelLogViewer();
    $files = $viewer->getFiles(true);

    if ($file = $request->query('file')) {
        abort_unless(in_array($file, $files, true), 404);
        $viewer->setFile($file);
    }

    // Ignore anything that isn't a known level.
    $level = in_array($request->query('level'), self::LEVELS, true)
        ? $request->query('level')
        : null;

    $logs = $viewer->all() ?? [];

    if ($level) {
        $logs = array_values(array_filter(
            $logs,
            fn ($log) => strtolower($log['level'] ?? '') === $level,
        ));
    }

    return Inertia::render('Admin/Logs/Index', [
        'files' => $files,
        'currentFile' => $viewer->getFileName(),
        'levels' => self::LEVELS,
        'currentLevel' => $level,
        'total' => count($logs),
        'logs' => array_slice($logs, 0, self::MAX_ENTRIES),
    ]);
}}