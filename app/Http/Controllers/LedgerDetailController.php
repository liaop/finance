<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\LedgerDetail;
use Illuminate\Http\Request;

class LedgerDetailController extends Controller
{
    public function index(Request $request, string $ledgerId)
    {
        $ledger = Ledger::where('user_id', $request->user()->id)
            ->with('details')
            ->findOrFail($ledgerId);

        return response()->json($ledger->details);
    }

    public function store(Request $request, string $ledgerId)
    {
        $ledger = Ledger::where('user_id', $request->user()->id)
            ->findOrFail($ledgerId);

        $validated = $request->validate([
            'type' => 'required|string|in:income,expense',
            'category' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:3',
            'occurred_at' => 'required|date',
            'description' => 'nullable|string|max:255',
            'merchant' => 'nullable|string|max:100',
            'attachment_url' => 'nullable|string|max:255',
        ]);

        $detail = $ledger->details()->create($validated);

        return response()->json($detail, 201);
    }

    public function show(Request $request, string $ledgerId, string $id)
    {
        $ledger = Ledger::where('user_id', $request->user()->id)
            ->findOrFail($ledgerId);

        $detail = $ledger->details()->findOrFail($id);

        return response()->json($detail);
    }

    public function update(Request $request, string $ledgerId, string $id)
    {
        $ledger = Ledger::where('user_id', $request->user()->id)
            ->findOrFail($ledgerId);

        $detail = $ledger->details()->findOrFail($id);

        $validated = $request->validate([
            'type' => 'sometimes|required|string|in:income,expense',
            'category' => 'sometimes|required|string|max:50',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'currency' => 'sometimes|required|string|max:3',
            'occurred_at' => 'sometimes|required|date',
            'description' => 'nullable|string|max:255',
            'merchant' => 'nullable|string|max:100',
            'attachment_url' => 'nullable|string|max:255',
        ]);

        $detail->update($validated);

        return response()->json($detail);
    }

    public function destroy(Request $request, string $ledgerId, string $id)
    {
        $ledger = Ledger::where('user_id', $request->user()->id)
            ->findOrFail($ledgerId);

        $detail = $ledger->details()->findOrFail($id);

        $detail->delete();

        return response()->noContent();
    }
}
