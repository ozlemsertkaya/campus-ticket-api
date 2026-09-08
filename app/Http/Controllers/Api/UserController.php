<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Get(path: "/api/users", summary: "Personelleri listele", tags: ["Users"], responses: [new OA\Response(response: 200, description: "Liste döndü")])]
    public function index()
    {
        return User::paginate(15);
    }
    #[OA\Post(
        path: "/api/users",
        summary: "Personel oluştur",
        tags: ["Users"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password"],
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "email", type: "string"),
                    new OA\Property(property: "password", type: "string")
                ]
            )
        ),
        responses: [new OA\Response(response: 201, description: "Oluşturuldu")]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:agent,admin', //sadece bu ikisinden birini kabul ediyor.
        ]);

        $data['password'] = bcrypt($data['password']); //şifreyi düz metin olarak kaydetmiyoruz,şifreliyoruz.

        return response()->json(User::create($data), 201);
    }
    #[OA\Get(path: "/api/users/{user}", summary: "Personel detayı", tags: ["Users"], responses: [new OA\Response(response: 200, description: "Detay döndü")])]
    public function show(User $user)
    {
        return $user;
    }
    #[OA\Put(
        path: "/api/users/{user}",
        summary: "Personel güncelle",
        tags: ["Users"],
        parameters: [new OA\Parameter(name: "user", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "email", type: "string")
                ]
            )
        ),
        responses: [new OA\Response(response: 200, description: "Güncellendi")]
    )]
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'role' => 'sometimes|in:agent,admin',
        ]);

        $user->update($data);

        return $user;
    }
    #[OA\Delete(path: "/api/users/{user}", summary: "Personel sil", tags: ["Users"], parameters: [new OA\Parameter(name: "user", in: "path", required: true, schema: new OA\Schema(type: "integer"))], responses: [new OA\Response(response: 204, description: "Silindi")])]
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
