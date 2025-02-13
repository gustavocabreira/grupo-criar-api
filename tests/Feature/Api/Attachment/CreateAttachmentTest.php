<?php

use App\Models\Attachment;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('it should be able to create an attachment', function () {
    $model = new Attachment();
    Storage::fake('temp');

    $payload = [
        'image' => UploadedFile::fake()->image('example.jpg'),
    ];

    $response = $this->postJson(route('api.attachments.store'), $payload);

    $response->assertStatus(Response::HTTP_CREATED);

    $this->assertDatabaseHas($model->getTable(), [
        'id' => $response->json()['id'],
        'filename' => $payload['image']->getClientOriginalName(),
    ]);

    $this->assertDatabaseCount($model->getTable(), 1);

    Storage::disk('temp')->assertExists($response->json()['path']);
});
