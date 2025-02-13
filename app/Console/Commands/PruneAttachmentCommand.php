<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneAttachmentCommand extends Command
{
    protected $signature = 'attachment:prune';

    protected $description = 'Prune attachments that are not linked to any product';

    public function handle(): void
    {
        Attachment::query()
            ->whereDoesntHave('products')
            ->where('created_at', '<=', now()->subHours(24))
            ->get()
            ->each(function ($attachment) {
                Storage::disk('public')->delete($attachment->path);
                $attachment->delete();
            });
    }
}
