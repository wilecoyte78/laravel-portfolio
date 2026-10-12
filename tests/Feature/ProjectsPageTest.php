<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProjectsPageTest extends TestCase
{
    public function test_projects_page_is_public_and_renders_the_projects_component(): void
    {
        $this->get(route('projects'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/Projects')
            );
    }
}
