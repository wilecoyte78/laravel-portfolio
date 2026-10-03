<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PageSeoTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_can_create_a_page_with_seo_fields(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.pages.store'), [
                'title' => 'Bio',
                'slug' => 'bio',
                'content' => '<p>Hello</p>',
                'meta_title' => 'Ben Mastrangelo | Laravel Developer Bio',
                'meta_description' => 'Senior Laravel developer based in Texas.',
                'meta_keywords' => 'php, laravel, vue',
                'is_published' => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pages', [
            'slug' => 'bio',
            'meta_title' => 'Ben Mastrangelo | Laravel Developer Bio',
            'meta_description' => 'Senior Laravel developer based in Texas.',
            'meta_keywords' => 'php, laravel, vue',
        ]);
    }

    public function test_admin_can_update_seo_fields(): void
    {
        $page = Page::create(['title' => 'Bio', 'slug' => 'bio', 'is_published' => true]);

        $this->actingAs($this->admin())
            ->put(route('admin.pages.update', $page), [
                'title' => 'Bio',
                'slug' => 'bio',
                'meta_title' => 'New SEO title',
                'meta_description' => 'New description',
                'meta_keywords' => 'one, two',
                'is_published' => true,
            ])
            ->assertRedirect();

        $page->refresh();

        $this->assertSame('New SEO title', $page->meta_title);
        $this->assertSame('New description', $page->meta_description);
        $this->assertSame('one, two', $page->meta_keywords);
    }

    public function test_seo_fields_are_optional(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.pages.store'), [
                'title' => 'Plain',
                'is_published' => true,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pages', ['slug' => 'plain', 'meta_title' => null]);
    }

    public function test_seo_fields_are_length_limited(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.pages.store'), [
                'title' => 'Too long',
                'meta_title' => str_repeat('a', 256),
                'meta_keywords' => str_repeat('b', 256),
            ])
            ->assertSessionHasErrors(['meta_title', 'meta_keywords']);
    }

    public function test_public_page_receives_seo_fields_and_canonical_url(): void
    {
        Page::create([
            'title' => 'Bio',
            'slug' => 'bio',
            'is_published' => true,
            'meta_title' => 'Custom SEO title',
            'meta_description' => 'Custom description',
            'meta_keywords' => 'php, laravel',
        ]);

        $this->get('/bio')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/Page')
                ->where('page.meta_title', 'Custom SEO title')
                ->where('page.meta_description', 'Custom description')
                ->where('page.meta_keywords', 'php, laravel')
                ->where('seo.url', url('/bio'))
            );
    }
}
