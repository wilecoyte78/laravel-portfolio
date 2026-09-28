<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Creates a starting "Home" page (slug: home) if one doesn't exist yet.
 * Uses firstOrCreate rather than updateOrCreate so it never overwrites
 * content the admin has already edited on subsequent deploys.
 */
class HomePageSeeder extends Seeder
{
    public function run(): void
    {
        Page::firstOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'content' => "<h1>Hi, I'm [Your Name]</h1><p>Welcome to my portfolio.</p>",
                'meta_description' => null,
                'is_published' => true,
            ]
        );
    }
}
