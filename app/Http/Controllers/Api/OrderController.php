<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OrderCreated;
use App\Models\Artwork;
use App\Models\Order;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class OrderController extends Controller
{
    public function capture(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'artwork_id' => ['required', 'integer', 'exists:artworks,id'],
            'paypal_order_id' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9_-]+$/'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'shipping_address' => ['required', 'array'],
            'shipping_address.address_line_1' => ['required', 'string', 'max:255'],
            'shipping_address.admin_area_2' => ['required', 'string', 'max:255'],
            'shipping_address.postal_code' => ['required', 'string', 'max:30'],
            'shipping_address.country_code' => ['required', 'string', 'size:2'],
        ]);

        $existingOrder = Order::query()
            ->where('paypal_order_id', $validated['paypal_order_id'])
            ->first();

        if ($existingOrder !== null) {
            return response()->json($existingOrder->load('artwork'), 200);
        }

        try {
            $paypalOrder = $this->capturePayPalOrder($validated['paypal_order_id']);
        } catch (RequestException) {
            return response()->json([
                'message' => 'La transaction PayPal n’a pas pu être vérifiée.',
            ], 422);
        } catch (Throwable) {
            return response()->json([
                'message' => 'Le service PayPal est momentanément indisponible.',
            ], 503);
        }

        if (($paypalOrder['status'] ?? null) !== 'COMPLETED') {
            return response()->json([
                'message' => 'La transaction PayPal n’est pas finalisée.',
            ], 422);
        }

        $order = DB::transaction(function () use ($validated, $paypalOrder): Order {
            $artwork = Artwork::query()
                ->lockForUpdate()
                ->findOrFail($validated['artwork_id']);

            if (! $artwork->is_for_sale || $artwork->status === 'sold' || $artwork->price === null) {
                abort(409, 'Cette œuvre n’est plus disponible à la vente.');
            }

            $paypalAmount = $paypalOrder['purchase_units'][0]['amount']['value'] ?? null;
            $paypalCurrency = $paypalOrder['purchase_units'][0]['amount']['currency_code'] ?? null;

            if (
                $paypalCurrency !== config('services.paypal.currency')
                || $this->formatAmount($paypalAmount) !== $this->formatAmount($artwork->price)
            ) {
                abort(422, 'Le montant PayPal ne correspond pas au prix de l’œuvre.');
            }

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'artwork_id' => $artwork->id,
                'paypal_order_id' => $validated['paypal_order_id'],
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'shipping_address' => $validated['shipping_address'],
                'amount' => $artwork->price,
                'status' => 'paid',
            ]);

            $artwork->update(['status' => 'sold']);

            return $order->load('artwork');
        });

        Mail::to(config('services.orders.admin_email'))
            ->send(new OrderCreated($order));

        return response()->json($order, 201);
    }

    /**
     * @return array<string, mixed>
     */
    private function capturePayPalOrder(string $paypalOrderId): array
    {
        $accessToken = Http::asForm()
            ->withBasicAuth(
                (string) config('services.paypal.client_id'),
                (string) config('services.paypal.client_secret'),
            )
            ->acceptJson()
            ->connectTimeout(3)
            ->timeout(10)
            ->post(config('services.paypal.base_url').'/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ])
            ->throw()
            ->json('access_token');

        return Http::withToken((string) $accessToken)
            ->acceptJson()
            ->connectTimeout(3)
            ->timeout(10)
            ->post(config('services.paypal.base_url').'/v2/checkout/orders/'.$paypalOrderId.'/capture')
            ->throw()
            ->json();
    }

    private function generateOrderNumber(): string
    {
        return 'MP-'.now()->format('Ymd').'-'.strtoupper(Str::random(8));
    }

    private function formatAmount(mixed $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }
}
