<?php

namespace Tests\Feature;

use App\Models\Ledger;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LedgerCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_ledger(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/ledgers', [
                'name' => 'My ledger',
                'currency' => 'USD',
                'description' => 'Personal finance ledger',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'My ledger',
                'currency' => 'USD',
                'description' => 'Personal finance ledger',
            ]);

        $this->assertDatabaseHas('ledgers', [
            'user_id' => $user->id,
            'name' => 'My ledger',
        ]);
    }

    public function test_authenticated_user_can_view_their_ledgers(): void
    {
        $user = User::factory()->create();
        Ledger::factory()->for($user)->create(['name' => 'User ledger']);
        Ledger::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/ledgers');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'User ledger']);
    }

    public function test_authenticated_user_can_view_a_single_ledger(): void
    {
        $user = User::factory()->create();
        $ledger = Ledger::factory()->for($user)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/ledgers/{$ledger->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $ledger->id]);
    }

    public function test_authenticated_user_can_update_their_ledger(): void
    {
        $user = User::factory()->create();
        $ledger = Ledger::factory()->for($user)->create([
            'name' => 'Old name',
            'currency' => 'CNY',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/ledgers/{$ledger->id}", [
                'name' => 'Updated ledger',
                'currency' => 'EUR',
            ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Updated ledger', 'currency' => 'EUR']);

        $this->assertDatabaseHas('ledgers', [
            'id' => $ledger->id,
            'name' => 'Updated ledger',
            'currency' => 'EUR',
        ]);
    }

    public function test_authenticated_user_can_delete_their_ledger(): void
    {
        $user = User::factory()->create();
        $ledger = Ledger::factory()->for($user)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/ledgers/{$ledger->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('ledgers', ['id' => $ledger->id]);
    }

    public function test_user_cannot_access_other_users_ledger(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $ledger = Ledger::factory()->for($owner)->create();

        $response = $this->actingAs($other, 'sanctum')
            ->getJson("/ledgers/{$ledger->id}");

        $response->assertNotFound();
    }
}
