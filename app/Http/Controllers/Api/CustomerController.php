<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use OpenApi\Attributes as OA;

class CustomerController extends Controller
{
    #[OA\Get(path: "/api/customers", summary: "Müşterileri listele", tags: ["Customers"], responses: [new OA\Response(response: 200, description: "Liste döndü")])]
    public function index()
    {
        return Customer::paginate(15); //tüm kategorileri sayfa sayfa döndür.(her sayfada 15)
    }
    #[OA\Post(
        path: "/api/customers",
        summary: "Yeni müşteri oluştur",
        tags: ["Customers"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email"],
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "email", type: "string"),
                    new OA\Property(property: "phone", type: "string")
                ]
            )
        ),
        responses: [new OA\Response(response: 201, description: "Oluşturuldu")]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email', //bu email customers tablosunda daha önce kullanılmamış olmalı
            'student_number' => 'nullable|string',
        ]);
        return response()->json(Customer::create($data), 201);
    }
    #[OA\Get(path: "/api/customers/{customer}", summary: "Müşteri detayı", tags: ["Customers"], parameters: [new OA\Parameter(name: "customer", in: "path", required: true, schema: new OA\Schema(type: "integer"))], responses: [new OA\Response(response: 200, description: "Detay döndü")])]
    public function show(Customer $customer)
    {
        return $customer;
    }
    #[OA\Put(
        path: "/api/customers/{customer}",
        summary: "Müşteri güncelle",
        tags: ["Customers"],
        parameters: [new OA\Parameter(name: "customer", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "email", type: "string"),
                    new OA\Property(property: "phone", type: "string")
                ]
            )
        ),
        responses: [new OA\Response(response: 200, description: "Güncellendi")]
    )]
    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email', //bu email customers tablosunda daha önce kullanılmamış olmalı
            'student_number' => 'nullable|string',
        ]);
        $customer->update($data);
        return $customer;
    }
    #[OA\Delete(path: "/api/customers/{customer}", summary: "Müşteri sil", tags: ["Customers"], parameters: [new OA\Parameter(name: "customer", in: "path", required: true, schema: new OA\Schema(type: "integer"))], responses: [new OA\Response(response: 204, description: "Silindi")])]
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(null, 204); //başarılı ama geri dönecek bir içerik yok.
    }
}
