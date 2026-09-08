<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    #[OA\Get(path: "/api/categories", summary: "Kategorileri listele", tags: ["Categories"], responses: [new OA\Response(response: 200, description: "Liste döndü")])]
    public function index()
    {
        return Category::paginate(15); //tüm kategorileri sayfa sayfa döndür.(her sayfada 15)
    }
    #[OA\Post(
        path: "/api/categories",
        summary: "Yeni kategori oluştur",
        tags: ["Categories"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [new OA\Property(property: "name", type: "string")]
            )
        ),
        responses: [new OA\Response(response: 201, description: "Oluşturuldu")]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        return response()->json(Category::create($data), 201);
    }
    #[OA\Get(
        path: "/api/categories/{category}",
        summary: "Kategori detayı",
        tags: ["Categories"],
        parameters: [new OA\Parameter(name: "category", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        responses: [new OA\Response(response: 200, description: "Detay döndü")]
    )]
    public function show(Category $category)
    {
        return $category;
    }
    #[OA\Put(path: "/api/categories/{category}", summary: "Kategori güncelle", tags: ["Categories"], parameters: [new OA\Parameter(name: "Category", in: "path", required: true, schema: new OA\Schema(type: "integer"))], responses: [new OA\Response(response: 200, description: "Güncellendi")])]
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($data);
        return $category;
    }
    #[OA\Delete(path: "/api/categories/{category}", summary: "Kategori sil", tags: ["Categories"], parameters: [new OA\Parameter(name: "Category", in: "path", required: true, schema: new OA\Schema(type: "integer"))], responses: [new OA\Response(response: 204, description: "Silindi")])]
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(null, 204); //başarılı ama geri dönecek bir içerik yok.
    }
}
