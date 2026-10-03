<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class ResumeController extends Controller
{
    private const MAX_SIZE_KB = 10240;

    public function index(): Response
    {
        $path = $this->resumePath();

        $resume = null;

        if (File::exists($path)) {
            $modifiedAt = File::lastModified($path);

            $resume = [
                'filename' => $this->filename(),
                'url' => $this->resumeUrl(),
                'size' => File::size($path),
                'modified_at' => $modifiedAt,
            ];
        }

        return Inertia::render('Admin/Resume', [
            'resume' => $resume,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'resume' => [
                'required',
                'file',
                'mimes:pdf',
                'max:' . self::MAX_SIZE_KB,
            ],
        ]);

        $directory = $this->publicDirectory();
        $target = $this->resumePath();

        File::ensureDirectoryExists($directory);

        $uploadedFile = $request->file('resume');

        $temporaryFilename = '.resume-' . uniqid('', true) . '.tmp';
        $temporaryPath = $directory . DIRECTORY_SEPARATOR . $temporaryFilename;

        $backupPath = null;

        try {
            /*
             * First place the uploaded PDF in the public directory
             * under a temporary filename.
             */
            $uploadedFile->move($directory, $temporaryFilename);

            /*
             * If an existing resume exists, temporarily move it
             * out of the way so we can restore it if necessary.
             */
            if (File::exists($target)) {
                $backupFilename = '.resume-backup-' . uniqid('', true) . '.pdf';
                $backupPath = $directory . DIRECTORY_SEPARATOR . $backupFilename;

                if (! File::move($target, $backupPath)) {
                    throw new RuntimeException(
                        'Unable to prepare the existing resume for replacement.'
                    );
                }
            }

            /*
             * Install the new resume using the canonical filename.
             */
            if (! File::move($temporaryPath, $target)) {
                if ($backupPath && File::exists($backupPath)) {
                    File::move($backupPath, $target);
                }

                throw new RuntimeException(
                    'Unable to install the new resume PDF.'
                );
            }

            /*
             * New file is successfully installed.
             * The old resume can now be deleted.
             */
            if ($backupPath && File::exists($backupPath)) {
                File::delete($backupPath);
            }
        } catch (\Throwable $e) {
            if (File::exists($temporaryPath)) {
                File::delete($temporaryPath);
            }

            /*
             * Restore the previous resume if necessary.
             */
            if (
                $backupPath &&
                File::exists($backupPath) &&
                ! File::exists($target)
            ) {
                File::move($backupPath, $target);
            }

            report($e);

            return back()->with(
                'error',
                'The resume could not be updated. The existing PDF was left unchanged.'
            );
        }

        return to_route('admin.resume.index')
            ->with('success', 'Resume PDF updated successfully.');
    }

    private function filename(): string
    {
        return config('resume.filename', 'resume.pdf');
    }

    private function publicDirectory(): string
    {
        return config(
            'resume.public_path',
            public_path('files')
        );
    }

    private function resumePath(): string
    {
        return $this->publicDirectory()
            . DIRECTORY_SEPARATOR
            . $this->filename();
    }

    private function resumeUrl(): string
    {
        return '/files/' . $this->filename();
    }
}