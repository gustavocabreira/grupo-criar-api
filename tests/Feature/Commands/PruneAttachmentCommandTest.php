<?php

use App\Models\Attachment;
use App\Models\Product;
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
    $this->assertDatabaseMissing('product_attachment_pivot', [
        'attachment_id' => $response->json()['id'],
    ]);
});

test('it should not delete attachments that are linked to a product', function () {
    Storage::fake('public');

    $image = UploadedFile::fake()->image('example.jpg');
    $path = $image->store('public', 'public');
    $attachment = Attachment::factory()->create([
        'path' => $path,
    ]);
    $product = Product::factory()->create();
    $product->attachments()->attach($attachment->id);

    $this->artisan('attachment:prune');

    Storage::disk('public')->assertExists($path);
    $this->assertDatabaseHas('product_attachment_pivot', [
        'attachment_id' => $attachment->id,
        'product_id' => $product->id,
    ]);
});
