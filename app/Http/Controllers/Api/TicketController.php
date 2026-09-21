<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Http\Request;
use App\Http\Resources\TicketResource;
use OpenApi\Attributes as OA;



class TicketController extends Controller
{
    public function __construct(protected TicketService $ticketService)
    { //Bu Controller her çağrıldığında, Laravel otomatik olarak bir TicketService nesnesi oluşturup bana versin, ben de onu $this->ticketService olarak sınıfın her metodunda kullanabileyim.

    }

    #[OA\Post(
        path: "/api/tickets/{ticket}/assign",
        summary: "Talebi bir personele ata",
        tags: ["Tickets"],
        parameters: [
            new OA\Parameter(name: "ticket", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["user_id"],
                properties: [new OA\Property(property: "user_id", type: "integer")]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Atandı")
        ]
    )]
    public function assign(Request $request, Ticket $ticket)
    {
        $userId = $request->input('user_id') ?? $request->user()?->id;
        //talebi üzerime al butonuna bastığında hiçbir parametre göndermese bile id üzerinden bilet direkt onun üzerine geçr.
        $user = User::findOrFail($userId);
        $updated = $this->ticketService->assign($ticket, $user);
        return response()->json([
            'message' => 'Talep başarıyla atandı.',
            'ticket' => $updated
        ]);
    }
    #[OA\Post(
        path: "/api/tickets/{ticket}/resolve",
        summary: "Talebi çözüldü olarak işaretle",
        tags: ["Tickets"],
        parameters: [
            new OA\Parameter(name: "ticket", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [new OA\Response(response: 200, description: "Çözüldü")]
    )]
    public function resolve(Ticket $ticket)
    {
        $updated = $this->ticketService->resolve($ticket);
        return response()->json($updated);
    }
    #[OA\Post(
        path: "api/tickets/{ticket}/close",
        summary: "Talebi kapat",
        tags: ["Tickets"],
        parameters: [
            new OA\Parameter(name: "ticket", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [new OA\Response(response: 200, description: "Kapatıldı")]
    )]
    public function close(Request $request, Ticket $ticket)
    {
        $closedBy = User::findOrFail($request->input('closed_by'));
        $updated = $this->ticketService->close($ticket, $closedBy);
        return response()->json($updated);
    }
    #[OA\Post(
        path: "/api/tickets/{ticket}/messages",
        summary: "Talebe mesaj ekle",
        parameters: [
            new OA\Response(response: 200, description: "Mesaj eklendi")
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["message"],
                properties: [new OA\Property(property: "message", type: "string")]
            )
        ),
    )]
    public function addMessage(Request $request, Ticket $ticket)
    {
        $senderType = $request->input('sender_type');
        $senderId = $request->input('sender_id');

        $sender = $senderType === 'user'
            ? User::findOrFail($senderId) //user ise user ı bul değilse customer ı bul
            : \App\Models\Customer::findOrFail($senderId);

        $message = $this->ticketService->addMessage($ticket, $sender, $request->input('message'));
        return response()->json($message);
    }
    #[OA\Post(
        path: "/api/tickets",
        summary: "Yeni talep oluştur",
        tags: ["Tickets"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "description"],
                properties: [
                    new OA\Property(property: "title", type: "string"),
                    new OA\Property(property: "description", type: "string"),
                    new OA\Property(property: "category_id", type: "integer"),
                    new OA\Property(property: "priority_id", type: "integer"),
                    new OA\Property(property: "customer_id", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Oluşturuldu")
        ]
    )]
    public function create(Request $request)
    {
        // 1. Canlı veritabanında 1 numaralı kategori yoksa ekle
        \Illuminate\Support\Facades\DB::statement("
            INSERT INTO categories (id, name, created_at, updated_at) 
            VALUES (1, 'Genel Arıza / Teknik', NOW(), NOW()) 
            ON CONFLICT (id) DO NOTHING;
        ");

        // 2. Canlı veritabanında öncelikler yoksa garantiye al
        \Illuminate\Support\Facades\DB::statement("
            INSERT INTO priorities (id, name, created_at, updated_at) VALUES 
            (1, 'Düşük', NOW(), NOW()),
            (2, 'Orta', NOW(), NOW()),
            (3, 'Yüksek', NOW(), NOW()),
            (4, 'Acil', NOW(), NOW())
            ON CONFLICT (id) DO NOTHING;
        ");

        // 3. PostgreSQL sequence sayaçlarını senkronize et
        try {
            \Illuminate\Support\Facades\DB::statement("SELECT setval(pg_get_serial_sequence('categories', 'id'), COALESCE((SELECT MAX(id) FROM categories), 1));");
            \Illuminate\Support\Facades\DB::statement("SELECT setval(pg_get_serial_sequence('priorities', 'id'), COALESCE((SELECT MAX(id) FROM priorities), 1));");
        } catch (\Throwable $e) {
            // SQLite veya sequence bulunamama durumunda devam et
        }

        // 4. Veriyi hazırla ve doğrudan Ticket modeline yaz
        $ticket = Ticket::create([
            'customer_id' => $request->user()?->id ?? 1,
            'category_id' => 1,
            'priority_id' => $request->input('priority_id') ?? 2,
            'title'       => $request->input('title'),
            'description' => $request->input('description'),
            'status'      => 'open',
        ]);

        return response()->json($ticket, 201);
    }
    #[OA\Get(
        path: "/api/tickets",
        summary: "Talepleri listele(filtreli,sayfalı)",
        tags: ["Tickets"],
        parameters: [
            new OA\Parameter(name: "status", in: "query", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "category_id", in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "priority_id", in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "customer_id", in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "assigned_to", in: "query", required: false, schema: new OA\Schema(type: "integer")),
        ],
        responses: [new OA\Response(response: 200, description: "Liste döndü")]
    )]
    public function index(Request $request)
    {
        $query = Ticket::query()->with(['customer', 'category', 'priority', 'assignedUser']);
        $user = $request->user();
        if ($user && $user->role === 'student') {
            $query->where('customer_id', $user->id);
        }
        if ($request->filled('status')) { //istekte status diye bir pparametre var mjı ve boş değil mi?kontrolü yapar.
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('priority_id')) {
            $query->where('priority_id', $request->input('priority_id'));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->input('assigned_to'));
        }
        return TicketResource::collection($query->latest()->paginate(15));
    }
    #[OA\Get(
        path: "/api/tickets/{ticket}",
        summary: "Talep detayını göster",
        parameters: [
            new OA\Parameter(name: "ticket", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [new OA\Response(response: 200, description: "Detay döndü")]
    )]
    public function show(Ticket $ticket)
    {
        $ticket->load(['customer', 'category', 'priority', 'assignedUser', 'messages', 'attachments']);

        return new TicketResource($ticket);
    }
    #[OA\Put(
        path: "/api/tickets/{ticket}",
        summary: "Talebi güncelle",
        tags: ["Tickets"],
        parameters: [
            new OA\Parameter(name: "ticket", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "title", type: "string"),
                    new OA\Property(property: "description", type: "string"),
                    new OA\Property(property: "category_id", type: "integer"),
                    new OA\Property(property: "priority_id", type: "integer")
                ]
            )
        ),
        responses: [new OA\Response(response: 200, description: "Güncellendi")]
    )]
    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255', //sometimes:eğer bu alan gönderildiyse kontrol et, gönderilmediyse sorun değil der.
            'description' => 'sometimes|string',
        ]);
        $ticket->update($data);
        return $ticket;
    }
    public function getMessages(Ticket $ticket)
    {
        //Mesajları gönderen kullanıcı bilgisiyle birlikte eskiden yeniye çekiyoruz.
        $messages = $ticket->messages()->with('sender')->oldest()->get();
        return response()->json($messages);
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed'
        ]);
        $ticket = Ticket::findOrFail($id);
        $user = $request->user();
        //Destek personeli ya da admin tüm statüleri atayabilsin
        if (!in_array($user->role, ['agent', 'support', 'admin'])) {
            return response()->json(['message' => 'Bu işlem için yetkiniz yok.'], 403);
        }
        $ticket->status = $request->status;
        if ($request->status === 'resolved') {
            $ticket->resolved_at = now();
        }
        $ticket->save();
        return response()->json(['message' => 'Durum güncellendi', 'ticket' => $ticket]);
    }
    #[OA\Delete(
        path: "/api/tickets/{ticket}",
        summary: "Talebi sil",
        tags: ["Tickets"],
        parameters: [
            new OA\Parameter(name: "ticket", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [new OA\Response(response: 204, description: "Silindi")]
    )]
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return response()->json(null, 204);
    }
}
