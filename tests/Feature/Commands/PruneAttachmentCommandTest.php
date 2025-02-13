<?php

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('it should be able to prune attachments', function () {
    Storage::fake('public');

    $payload = [
        'image' => UploadedFile::fake()->image('example.jpg'),
    ];

    $response = $this->postJson(route('api.attachments.store'), $payload);

    Carbon::setTestNow(Carbon::now()->addDay());

    $this->artisan('attachment:prune');

    Storage::disk('public')->assertMissing($response->json()['path']);
});
