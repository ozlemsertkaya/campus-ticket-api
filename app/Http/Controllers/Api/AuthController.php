<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;


class AuthController extends Controller
{
    #[OA\Post(
        path: "/api/login",
        summary: "Giriş yap, token al",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "agent@ornek.com"),
                    new OA\Property(property: "password", type: "string", example: "123456"),
                ]
            )
        ),
        responses: [new OA\Response(response: 200, description: "Giriş başarılı, token döndü")]
    )]
    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) { //veritabanındaki hashlenmiş şifreyle karşılaştırıyor.
            return response()->json(['message' => 'Bilgiler hatalı.'], 401);
        }
        $token = $user->createToken('api-token')->plainTextToken; //Kullanıcı için yeni benzersiz token üretir.birden fazla giriş yapılırsa ayırt etmk için.
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "Çıkış yap, token'ı geçersiz kıl",
        tags: ["Auth"],
        responses: [new OA\Response(response: 200, description: "Çıkış yapıldı")]
    )]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete(); //logout sırasında kullanılan token ı veritabanından siler böylece bir daha kullanılamaz hale gelir.
        return response()->json(['message' => 'Çıkış yapıldı.']);
    }
}
