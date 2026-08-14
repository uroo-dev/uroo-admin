<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create();
    }

    private function makeClient(User $user): Client
    {
        return Client::create([
            'user_id' => $user->id,
            'name' => 'PT Test Indonesia',
            'whatsapp' => '628123456789',
            'status' => 'deal',
        ]);
    }

    private function storePayload(Client $client, array $overrides = []): array
    {
        return array_merge([
            'client_id' => $client->id,
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'items' => [
                ['description' => 'Jasa pembuatan aplikasi', 'quantity' => 1, 'rate' => 1000000],
            ],
            'status' => 'hutang',
            'paid_amount' => 0,
            'notes' => 'Invoice test',
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('invoices.index'))->assertRedirect(route('login'));
    }

    public function test_index_renders_invoices_with_stats(): void
    {
        $user = $this->makeUser();
        $client = $this->makeClient($user);
        Invoice::create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'invoice_number' => 'INV-2026-08-0001',
            'items' => [],
            'total' => 1000000,
            'paid_amount' => 0,
            'status' => 'hutang',
            'due_date' => now()->addDays(7),
        ]);

        $this->actingAs($user)
            ->get(route('invoices.index'))
            ->assertOk()
            ->assertSee('INV-2026-08-0001')
            ->assertSee('PT Test Indonesia');
    }

    public function test_user_can_create_invoice_without_payment(): void
    {
        $user = $this->makeUser();
        $client = $this->makeClient($user);

        $this->actingAs($user)
            ->post(route('invoices.store'), $this->storePayload($client))
            ->assertRedirect(route('invoices.index'));

        $invoice = Invoice::first();
        $this->assertNotNull($invoice);
        $this->assertSame('hutang', $invoice->status);
        $this->assertEquals(1000000, (float) $invoice->total);
        $this->assertEquals(0, (float) $invoice->paid_amount);
        $this->assertEquals(0, InvoicePayment::count());
    }

    public function test_user_can_create_invoice_with_full_payment(): void
    {
        $user = $this->makeUser();
        $client = $this->makeClient($user);

        $this->actingAs($user)
            ->post(route('invoices.store'), $this->storePayload($client, ['paid_amount' => 1000000]));

        $invoice = Invoice::first();
        $this->assertSame('lunas', $invoice->status);
        $this->assertEquals(1000000, (float) $invoice->paid_amount);
        $this->assertNotNull($invoice->paid_at);
        $this->assertEquals(1, InvoicePayment::count());
    }

    public function test_editing_invoice_does_not_double_count_payment(): void
    {
        $user = $this->makeUser();
        $client = $this->makeClient($user);
        $this->actingAs($user)->post(route('invoices.store'), $this->storePayload($client));
        $invoice = Invoice::first();

        $this->actingAs($user)
            ->put(route('invoices.update', $invoice), $this->storePayload($client, ['paid_amount' => 500000]));

        $invoice->refresh();
        $this->assertEquals(500000, (float) $invoice->paid_amount);
        $this->assertSame('hutang', $invoice->status);
    }

    public function test_user_can_update_payment_amount(): void
    {
        $user = $this->makeUser();
        $client = $this->makeClient($user);
        $this->actingAs($user)->post(route('invoices.store'), $this->storePayload($client));
        $invoice = Invoice::first();

        $this->actingAs($user)
            ->patch(route('invoices.update-payment', $invoice), ['paid_amount' => 750000]);

        $invoice->refresh();
        $this->assertEquals(750000, (float) $invoice->paid_amount);

        $this->actingAs($user)
            ->patch(route('invoices.update-payment', $invoice), ['paid_amount' => 100000]);

        $invoice->refresh();
        $this->assertEquals(100000, (float) $invoice->paid_amount);
    }

    public function test_updating_payment_to_full_marks_invoice_as_lunas(): void
    {
        $user = $this->makeUser();
        $client = $this->makeClient($user);
        $this->actingAs($user)->post(route('invoices.store'), $this->storePayload($client));
        $invoice = Invoice::first();

        $this->actingAs($user)
            ->patch(route('invoices.update-payment', $invoice), ['paid_amount' => 1000000]);

        $invoice->refresh();
        $this->assertSame('lunas', $invoice->status);
        $this->assertNotNull($invoice->paid_at);
    }

    public function test_reducing_payment_reopens_lunas_invoice(): void
    {
        $user = $this->makeUser();
        $client = $this->makeClient($user);
        $this->actingAs($user)->post(route('invoices.store'), $this->storePayload($client, ['paid_amount' => 1000000]));
        $invoice = Invoice::first();
        $this->assertSame('lunas', $invoice->status);

        $this->actingAs($user)
            ->patch(route('invoices.update-payment', $invoice), ['paid_amount' => 500000]);

        $invoice->refresh();
        $this->assertSame('hutang', $invoice->status);
        $this->assertNull($invoice->paid_at);
    }

    public function test_user_can_delete_invoice(): void
    {
        $user = $this->makeUser();
        $client = $this->makeClient($user);
        $this->actingAs($user)->post(route('invoices.store'), $this->storePayload($client));
        $invoice = Invoice::first();

        $this->actingAs($user)
            ->delete(route('invoices.destroy', $invoice))
            ->assertRedirect(route('invoices.index'));

        $this->assertSoftDeleted('invoices', ['id' => $invoice->id]);
    }

    public function test_report_page_renders_payment_history(): void
    {
        $user = $this->makeUser();
        $client = $this->makeClient($user);
        $this->actingAs($user)->post(route('invoices.store'), $this->storePayload($client, ['paid_amount' => 400000]));
        $invoice = Invoice::first();

        $this->actingAs($user)
            ->get(route('invoices.report', $invoice))
            ->assertOk()
            ->assertSee($invoice->invoice_number);
    }

    public function test_pdf_downloads(): void
    {
        $user = $this->makeUser();
        $client = $this->makeClient($user);
        $this->actingAs($user)->post(route('invoices.store'), $this->storePayload($client));
        $invoice = Invoice::first();

        $this->actingAs($user)
            ->get(route('invoices.pdf', $invoice))
            ->assertOk();
    }

    public function test_send_wa_redirects_to_wa_dot_me(): void
    {
        $user = $this->makeUser();
        $client = $this->makeClient($user);
        $this->actingAs($user)->post(route('invoices.store'), $this->storePayload($client));
        $invoice = Invoice::first();

        $this->actingAs($user)
            ->get(route('invoices.send-wa', $invoice))
            ->assertRedirect();
    }
}
