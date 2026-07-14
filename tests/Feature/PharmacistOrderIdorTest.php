<?php

namespace Tests\Feature;

use App\Models\CustomerOrder;
use App\Models\Pharmacy;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PharmacistOrderIdorTest extends TestCase
{
    use RefreshDatabase;

    private function makePharmacist(Pharmacy $pharmacy): User
    {
        $role = Role::firstOrCreate(['name' => 'pharmacist']);

        $user = User::factory()->create(['pharmacy_id' => $pharmacy->id]);
        $user->roles()->attach($role);

        return $user;
    }

    private function makeOrder(Pharmacy $pharmacy, User $customer): CustomerOrder
    {
        $order = new CustomerOrder();
        $order->id = (string) Str::uuid();
        $order->user_id = $customer->id;
        $order->number = 1;
        $order->count = 1;
        $order->total = 1000;
        $order->status = CustomerOrder::STATUS_EN_ATTENTE;
        $order->items = [['id' => 1, 'libelle' => 'Produit', 'prix' => 1000]];
        $order->pharmacy_id = $pharmacy->id;
        $order->save();

        return $order;
    }

    public function test_a_pharmacist_cannot_view_another_pharmacys_order(): void
    {
        $pharmacyA = Pharmacy::create(['name' => 'Pharmacie A', 'district' => 'Centre']);
        $pharmacyB = Pharmacy::create(['name' => 'Pharmacie B', 'district' => 'Nord']);

        $pharmacistA = $this->makePharmacist($pharmacyA);
        $customer = User::factory()->create();

        $orderFromPharmacyB = $this->makeOrder($pharmacyB, $customer);

        $response = $this->actingAs($pharmacistA)
            ->withSession(['pharmacist_2fa_ok' => true])
            ->get(route('pharmacist.orders.show', $orderFromPharmacyB));

        $response->assertForbidden();
    }

    public function test_a_pharmacist_can_view_their_own_pharmacys_order(): void
    {
        $pharmacyA = Pharmacy::create(['name' => 'Pharmacie A', 'district' => 'Centre']);

        $pharmacistA = $this->makePharmacist($pharmacyA);
        $customer = User::factory()->create();

        $orderFromPharmacyA = $this->makeOrder($pharmacyA, $customer);

        $response = $this->actingAs($pharmacistA)
            ->withSession(['pharmacist_2fa_ok' => true])
            ->get(route('pharmacist.orders.show', $orderFromPharmacyA));

        $response->assertOk();
    }
}
