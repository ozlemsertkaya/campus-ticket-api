<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.edu\.tr$/i'
            ],
            'password' => 'required|string|min:6',
            'role' => 'required|in:student,agent', //öğrenci mi personel mi doğrulaması
        ], [
            'email.regex' => 'Yalnızca üniversite e-postası (.edu.tr) ile kayıt olabilirsiniz!',
            'email.unique' => 'Bu e-posta adresiyle zaten bir hesap açılmış.',
            'password.min' => 'Şifreniz en az 6 karakter olmalıdır.',
        ]);
        $email = Str::lower($request->email);
        //rolü e postadan yakalıyoruz.
        $role = (Str::contains($email, 'ogr.') || Str::contains($email, 'ogrenci.')) ? 'student' : 'agent';
        //kullanııcyı oluştur
        $user = User::create([
            'name'     => $request->name,
            'email'    => $email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);
        //Sanctum ile token üret
        $token = $user->createToken('auth-token')->plainTextToken;
        return response()->json([
            'message' => 'Kayıt başarılı!',
            'token' => $token,
            'user' => $user
        ], 201);
    }
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

        $user = User::where('email', Str::lower($request->email))->first();
        //şifre veya kullanıcı yanlışsa
        if (!$user || !Hash::check($request->password, $user->password)) { //veritabanındaki hashlenmiş şifreyle karşılaştırıyor.
            return response()->json(['message' => 'Bilgiler hatalı.'], 401);
        }
        $token = $user->createToken('auth-token')->plainTextToken; //Kullanıcı için yeni benzersiz token üretir.birden fazla giriş yapılırsa ayırt etmk için.
        return response()->json([
            'message' => 'Giriş başarılı!',
            'token' => $token,
            'user' => $user
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
