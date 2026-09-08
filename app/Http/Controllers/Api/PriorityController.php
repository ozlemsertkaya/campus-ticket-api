<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Priority;
use OpenApi\Attributes as OA;

class PriorityController extends Controller
{
    #[OA\Get(path: "/api/priorities", summary: "Öncelikleri listele", tags: ["Priorities"], responses: [new OA\Response(response: 200, description: "Liste döndü")])]
    public function index()
    {
        return Priority::paginate(15);
    }
    #[OA\Post(path: "/api/priorities", summary: "Yeni öncelik oluştur", tags: ["Priorities"], requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [new OA\Property(property: "name", type: "string")]
        )
    ), responses: [new OA\Response(response: 201, description: "Oluşturuldu")])]
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1|max:3'
        ]);
        return response()->json(Priority::create($data), 201);
    }
    #[OA\Get(path: "/api/priorities/{priority}", summary: "Öncelik detayı", tags: ["Priorities"], parameters: [new OA\Parameter(name: "priority", in: "path", required: true, schema: new OA\Schema(type: "integer"))], responses: [new OA\Response(response: 200, description: "Detay döndü")])]
    public function show(Priority $priority)
    {
        return  $priority;
    }
    #[OA\Put(
        path: "/api/priorities/{priority}",
        summary: "Öncelik güncelle",
        tags: ["Priorities"],
        parameters: [new OA\Parameter(name: "priority", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [new OA\Property(property: "name", type: "string")]
            )
        ),
        responses: [new OA\Response(response: 200, description: "Güncellendi")]
    )]
    public function update(Request $request, Priority $priority)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $priority->update($data);
        return  $priority;
    }
    #[OA\Delete(path: "/api/priorities/{priority}", summary: "Öncelik sil", tags: ["Priorities"], parameters: [new OA\Parameter(name: "priority", in: "path", required: true, schema: new OA\Schema(type: "integer"))], responses: [new OA\Response(response: 204, description: "Silindi")])]
    public function destroy(Priority $priority)
    {
        $priority->delete();
        return response()->json(null, 204); //başarılı ama geri dönecek bir içerik yok.
    }
}
