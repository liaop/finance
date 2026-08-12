<?php

namespace Tests\Feature;

use App\Models\Ledger;
use App\Models\LedgerDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LedgerDetailCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_ledger_detail(): void
    {
        $user = User::factory()->create();
        $ledger = Ledger::factory()->for($user)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/ledgers/{$ledger->id}/details", [
                'type' => 'expense',
                'category' => 'food',
                'amount' => 88.50,
                'currency' => 'CNY',
                'occurred_at' => '2026-08-12 09:00:00',
                'description' => 'Office lunch',
                'merchant' => 'Canteen',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'type' => 'expense',
                'category' => 'food',
                'merchant' => 'Canteen',
            ]);

        $this->assertDatabaseHas('ledger_details', [
            'ledger_id' => $ledger->id,
            'category' => 'food',
            'type' => 'expense',
        ]);
    }

    public function test_authenticated_user_can_view_their_ledger_details(): void
    {
        $user = User::factory()->create();
        $ledger = Ledger::factory()->for($user)->create();
        LedgerDetail::factory()->for($ledger)->create(['category' => 'food']);

        $otherOwner = User::factory()->create();
        $otherLedger = Ledger::factory()->for($otherOwner)->create();
        LedgerDetail::factory()->for($otherLedger)->create(['category' => 'travel']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/ledgers/{$ledger->id}/details");

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['category' => 'food'])
            ->assertJsonMissing(['category' => 'travel']);
    }

    public function test_user_cannot_access_other_users_ledger_detail(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $ledger = Ledger::factory()->for($owner)->create();
        $detail = LedgerDetail::factory()->for($ledger)->create();

        $response = $this->actingAs($otherUser, 'sanctum')
            ->getJson("/ledgers/{$ledger->id}/details/{$detail->id}");

        $response->assertNotFound();
    }
}
