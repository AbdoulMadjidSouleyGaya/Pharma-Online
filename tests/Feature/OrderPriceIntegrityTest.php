<?php

namespace Tests\Feature;

use App\Models\CustomerOrder;
use App\Models\Pharmacy;
use App\Models\PharmaProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPriceIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_cannot_forge_the_order_total_via_items_price(): void
    {
        $user = User::factory()->create();

        $pharmacy = Pharmacy::create([
            'name'     => 'Pharmacie Test',
            'district' => 'Centre',
        ]);

        $product = PharmaProduct::create([
            'libelle'     => 'Paracétamol 500mg',
            'prix'        => 5000,
            'stock'       => 'Disponible',
            'quantity'    => 100,
            'pharmacy_id' => $pharmacy->id,
        ]);

        $response = $this->actingAs($user)->post(route('order.store'), [
            'pharmacy_id' => $pharmacy->id,
            'items' => [
                // Le client tente de forcer un prix dérisoire.
                ['id' => $product->id, 'qty' => 2, 'price' => 1],
            ],
        ]);

        $response->assertRedirect(route('order.current'));

        $order = CustomerOrder::where('user_id', $user->id)->firstOrFail();

        // Le total doit être recalculé depuis le prix réel en base (5000 * 2),
        // jamais depuis la valeur envoyée par le client.
        $this->assertSame(10000, (int) $order->total);
        $this->assertSame(5000, (int) $order->items[0]['price']);
    }

    public function test_a_product_from_another_pharmacy_is_ignored(): void
    {
        $user = User::factory()->create();

        $pharmacyA = Pharmacy::create(['name' => 'Pharmacie A', 'district' => 'Centre']);
        $pharmacyB = Pharmacy::create(['name' => 'Pharmacie B', 'district' => 'Nord']);

        $productFromOtherPharmacy = PharmaProduct::create([
            'libelle'     => 'Produit pharmacie B',
            'prix'        => 9999,
            'stock'       => 'Disponible',
            'quantity'    => 10,
            'pharmacy_id' => $pharmacyB->id,
        ]);

        $response = $this->actingAs($user)->post(route('order.store'), [
            'pharmacy_id' => $pharmacyA->id,
            'items' => [
                ['id' => $productFromOtherPharmacy->id, 'qty' => 1],
            ],
        ]);

        // Aucun produit valide pour cette pharmacie -> rejet, pas de commande créée.
        $response->assertRedirect();
        $this->assertSame(0, CustomerOrder::where('user_id', $user->id)->count());
    }
}
