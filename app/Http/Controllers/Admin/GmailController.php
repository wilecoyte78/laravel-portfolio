<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GmailController extends Controller
{
    public function connect(Request $request): RedirectResponse
    {
        $state = Str::random(40);
        $request->session()->put('gmail_oauth_state', $state);

        $query = http_build_query([
            'client_id' => config('gmail.client_id'),
            'redirect_uri' => config('gmail.redirect_uri'),
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/gmail.send',
            'access_type' => 'offline',
            // Forces Google to re-issue a refresh token even if this
            // account has authorized the app before (Google normally only
            // returns one on the very first consent).
            'prompt' => 'consent',
            'state' => $state,
        ]);

        return redirect("https://accounts.google.com/o/oauth2/v2/auth?{$query}");
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->get('state') !== $request->session()->pull('gmail_oauth_state')) {
            abort(403, 'Invalid OAuth state - please try connecting again.');
        }

        if ($request->has('error')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Gmail connection was cancelled: '.$request->get('error'));
        }

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('gmail.client_id'),
            'client_secret' => config('gmail.client_secret'),
            'redirect_uri' => config('gmail.redirect_uri'),
            'code' => $request->get('code'),
            'grant_type' => 'authorization_code',
        ]);

        if (! $response->successful() || ! $response->json('refresh_token')) {
            return redirect()->route('admin.dashboard')->with(
                'error',
                'Google did not return a refresh token. If you\'ve connected before, revoke access at '.
                'https://myaccount.google.com/permissions and try connecting again.'
            );
        }

        GoogleToken::query()->delete();
        GoogleToken::create(['refresh_token' => $response->json('refresh_token')]);

        return redirect()->route('admin.dashboard')->with('success', 'Gmail connected successfully.');
    }
}
