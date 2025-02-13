<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attachment\CreateAttachmentRequest;
use App\Models\Attachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AttachmentController extends Controller
{
    public function store(CreateAttachmentRequest $request): JsonResponse
    {
        $request->validated();

        $path = $request->file('image')->store('public', 'public');

        $attachment = Attachment::query()->create([
            'filename' => $request->file('image')->getClientOriginalName(),
            'path' => $path,
            'mime' => $request->file('image')->getMimeType(),
        ]);

        return response()->json($attachment, Response::HTTP_CREATED);
    }
}
