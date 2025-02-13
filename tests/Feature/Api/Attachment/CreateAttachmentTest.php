<?php

use App\Models\Attachment;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

dataset('valid_payload', [
    'images' => [
        ['image' => UploadedFile::fake()->image('example.jpg')],
        ['image' => UploadedFile::fake()->image('example.png')],
        ['image' => UploadedFile::fake()->image('example.jpeg')],
    ],
]);

test('it should be able to create an attachment', function ($payload) {
    $model = new Attachment();
    Storage::fake('temp');

    $response = $this->postJson(route('api.attachments.store'), $payload);

    $response->assertStatus(Response::HTTP_CREATED);

    $this->assertDatabaseHas($model->getTable(), [
        'id' => $response->json()['id'],
        'filename' => $payload['image']->getClientOriginalName(),
    ]);

    $this->assertDatabaseCount($model->getTable(), 1);

    Storage::disk('temp')->assertExists($response->json()['path']);
})->with('valid_payload');

dataset('invalid_payload', [
    'empty image' => [
        ['image' => null], ['image' => ['The image field is required.']],
    ],
    'invalid mime type' => [
        ['image' => UploadedFile::fake()->image('example.pdf')], ['image' => ['The image field must be a file of type: jpeg, png, jpg.', 'The image field must be an image.']],
    ],
    'invalid file size' => [
        ['image' => UploadedFile::fake()->image('example.jpg')->size(1024 * 1024 * 10)],
        ['image' => ['The image field must not be greater than 1024 kilobytes.']],
    ],
]);

test('it should return unprocessable entity when trying to create an attachment with an invalid payload', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);

    $model = new Attachment();

    $response = $this->postJson(route('api.attachments.store'), $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    $response->assertJsonValidationErrors($key);

    $response->assertJsonFragment([
        'errors' => [
            'image' => $expectedErrors['image'],
        ],
    ]);

    $this->assertDatabaseCount($model->getTable(), 0);
})->with('invalid_payload');
