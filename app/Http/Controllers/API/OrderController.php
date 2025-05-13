<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;


/**
 * @OA\Info(
 *     title="Sipariş Yönetim API",
 *     version="1.0.0",
 *     description="Sipariş oluşturma, listeleme, güncelleme ve silme işlemleri için API dökümantasyonu"
 * )
 *
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     title="Order",
 *     required={"customer_id", "product_id", "quantity", "status"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="customer_id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=2),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="status", type="string", example="processing"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Tüm siparişleri listeler",
     *     tags={"Orders"},
     *     @OA\Response(
     *         response=200,
     *         description="Başarılı",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Order"))
     *     )
     * )
     */
    public function index()
    {
        $orders = Order::with(['customer', 'product'])->get();

        return response()->json($orders);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Yeni sipariş oluşturur",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"customer_id", "product_id", "quantity", "status"},
     *             @OA\Property(property="customer_id", type="integer", example=1),
     *             @OA\Property(property="product_id", type="integer", example=2),
     *             @OA\Property(property="quantity", type="integer", example=3),
     *             @OA\Property(property="status", type="string", example="pending")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sipariş oluşturuldu",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     )
     * )
     */
    // Yeni sipariş oluştur
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'in:pending,processing,completed,cancelled',
        ]);

        $order = Order::create($validated);

        return response()->json($order->load('customer', 'product'), 201);
    }
    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Belirli siparişi getirir",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sipariş bulundu",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sipariş bulunamadı"
     *     )
     * )
     */

    // Belirli bir siparişi getir
    public function show($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Sipariş bulunamadı'], 404);
        }

        return response()->json($order, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     summary="Siparişi günceller",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="customer_name", type="string", example="Zeynep"),
     *             @OA\Property(property="product", type="string", example="Kulaklık"),
     *             @OA\Property(property="quantity", type="integer", example=2),
     *             @OA\Property(property="status", type="string", example="completed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sipariş güncellendi",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sipariş bulunamadı"
     *     )
     * )
     */
    // Siparişi güncelle
    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Sipariş bulunamadı'], 404);
        }

        $validated = $request->validate([
            'customer_name' => 'string',
            'product' => 'string',
            'quantity' => 'integer|min:1',
            'status' => 'in:pending,processing,completed,cancelled',
        ]);

        $order->update($validated);

        return response()->json($order, 200);
    }

    // Siparişi sil
        /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="Siparişi siler",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sipariş silindi"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sipariş bulunamadı"
     *     )
     * )
     */
    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Sipariş bulunamadı'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Sipariş silindi'], 200);
    }
}
