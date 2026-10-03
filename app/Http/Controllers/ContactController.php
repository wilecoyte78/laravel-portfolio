<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\GmailMailer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ContactController extends Controller
{
    public function show(): Response
    {
        $page = Page::where('slug', 'contact')->first();

        return Inertia::render('Public/Contact', [
            'page' => $page,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            // Honeypot: a real visitor never sees or fills this field
            // (hidden via CSS in the form), so any submission with it
            // filled in is almost certainly a bot.
            'website' => ['prohibited'],
        ]);

        try {
            app(GmailMailer::class)->send(
                replyToName: $data['name'],
                replyToEmail: $data['email'],
                subject: "New contact form message from {$data['name']}",
                body: $data['message'],
            );
        } catch (Throwable $e) {
            report($e);

            return back()->with(
                'error',
                'Sorry, something went wrong sending your message. Please try emailing directly instead.'
            );
        }

        return back()->with('success', "Thanks {$data['name']}, your message has been sent!");
    }
}
