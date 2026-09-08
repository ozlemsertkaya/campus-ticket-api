<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attachment;
use App\Models\Ticket;
use OpenApi\Attributes as OA;

class AttachmentController extends Controller
{
    #[OA\Post(
        path: "/api/tickets/{ticket}/attachments",
        summary: "Talebe dosya ekle",
        tags: ["Attachments"],
        parameters: [
            new OA\Parameter(name: "ticket", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["file"],
                    properties: [new OA\Property(property: "file", type: "string", format: "binary")]
                )
            )
        ),
        responses: [new OA\Response(response: 201, description: "Dosya yüklendi")]
    )]
    public function store(Request $request, Ticket $ticket)
    {

        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $path = $request->file('file')->store('attachments', 'public'); //dosyayı storage/app/public/attachments/ klasörüne kaydediyor.
        $attachment = $ticket->attachments()->create([ //attachable_type ve attachable_id'yi Laravel otomatik dolduruyor.
            'file_path' => $path,
            'original_name' => $request->file('file')->getClientOriginalName(),
            'mime_type' => $request->file('file')->getMimeType(),
        ]);
        return response()->json($attachment, 201);
    }
    #[OA\Delete(
        path: "/api/tickets/{ticket}/attachments",
        summary: "Dosyayı sil",
        tags: ["Attachments"],
        parameters: [
            new OA\Parameter(name: "ticket", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [new OA\Response(response: 204, description: "Dosya silindi")]
    )]
    public function destroy(Attachment $attachment)
    {
        $attachment->delete();
        return response()->json(null, 204);
    }
}
