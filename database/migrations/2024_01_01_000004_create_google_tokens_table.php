<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Single-row table (id=1) storing the OAuth refresh token used to
        // send mail via the Gmail API on behalf of the connected account.
        Schema::create('google_tokens', function (Blueprint $table) {
            $table->id();
            $table->text('refresh_token');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_tokens');
    }
};
