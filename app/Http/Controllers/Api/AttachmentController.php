<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AttachmentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg',
                'max:1024',
            ],
        ]);

        $path = $request->file('image')->store('temp', 'temp');

        $attachment = Attachment::query()->create([
            'filename' => $request->file('image')->getClientOriginalName(),
            'path' => $path,
            'mime' => $request->file('image')->getMimeType(),
        ]);

        return response()->json($attachment, Response::HTTP_CREATED);
    }
}
