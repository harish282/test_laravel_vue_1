<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('user.{id}', function ($user, $id) {
    Log::info('Broadcast auth', ['user' => $user ? $user->id : null, 'id' => $id]);
    return true; // Skip auth check for testing
});
