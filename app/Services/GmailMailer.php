<?php

namespace App\Services;

use App\Models\GoogleToken;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GmailMailer
{
    /**
     * Send an email via the Gmail API, using the stored OAuth refresh
     * token to obtain a short-lived access token on each send.
     *
     * @throws RuntimeException if Gmail isn't connected yet, or if either
     *                          the token refresh or the send call fails.
     */
    public function send(string $replyToName, string $replyToEmail, string $subject, string $body): void
    {
        $refreshToken = GoogleToken::currentRefreshToken();

        if (! $refreshToken) {
            throw new RuntimeException('Gmail is not connected yet. Connect it from the admin dashboard first.');
        }

        $accessToken = $this->refreshAccessToken($refreshToken);
        $to = config('gmail.contact_to');

        if (! $to) {
            throw new RuntimeException('CONTACT_TO_EMAIL is not configured.');
        }

        $raw = $this->buildRawMessage($to, $replyToName, $replyToEmail, $subject, $body);

        $response = Http::withToken($accessToken)
            ->post('https://www.googleapis.com/gmail/v1/users/me/messages/send', [
                'raw' => $raw,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Gmail API send failed: '.$response->body());
        }
    }

    private function refreshAccessToken(string $refreshToken): string
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('gmail.client_id'),
            'client_secret' => config('gmail.client_secret'),
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ]);

        if (! $response->successful() || ! $response->json('access_token')) {
            throw new RuntimeException('Could not refresh Gmail access token: '.$response->body());
        }

        return $response->json('access_token');
    }

    private function buildRawMessage(string $to, string $replyToName, string $replyToEmail, string $subject, string $body): string
    {
        $encodedSubject = '=?UTF-8?B?'.base64_encode($subject).'?=';

        $message = "To: {$to}\r\n";
        $message .= "Reply-To: {$replyToName} <{$replyToEmail}>\r\n";
        $message .= "Subject: {$encodedSubject}\r\n";
        $message .= "MIME-Version: 1.0\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
        $message .= $body;

        // Gmail API requires URL-safe base64 (RFC 4648 §5), no padding.
        return rtrim(strtr(base64_encode($message), '+/', '-_'), '=');
    }
}
