<?php

namespace App\Http\Controllers;

use App\Models\ProjectAttachment;
use Illuminate\Support\Facades\Storage;

class ProjectAttachmentController extends Controller
{
    public function destroy(ProjectAttachment $attachment)
    {
        $this->authorize('delete', $attachment);

        Storage::disk('local')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('success', 'Attachment deleted');
    }
}
