<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Resume Filename
    |--------------------------------------------------------------------------
    |
    | This is the public filename used for the current resume.
    |
    */

    'filename' => 'resume.pdf',

    /*
    |--------------------------------------------------------------------------
    | Public Files Path
    |--------------------------------------------------------------------------
    |
    | Local development defaults to Laravel's public/files directory.
    |
    | Production can override this with RESUME_PUBLIC_PATH in .env.
    |
    */

    'public_path' => env(
        'RESUME_PUBLIC_PATH',
        public_path('files')
    ),
];