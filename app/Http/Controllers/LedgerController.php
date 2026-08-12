<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ledger;

class LedgerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $ledgers = Ledger::where('user_id', $request->user()->id)->get();

        return response()->json($ledgers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'currency' => 'required|string|max:3',
            'description' => 'nullable|string',
        ]);

        $ledger = Ledger::create(array_merge($validated, ['user_id' => $request->user()->id]));

        return response()->json($ledger, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $ledger = Ledger::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json($ledger);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'currency' => 'sometimes|required|string|max:3',
            'description' => 'nullable|string',
        ]);

        $ledger = Ledger::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $ledger->update($validated);

        return response()->json($ledger);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $ledger = Ledger::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $ledger->delete();

        return response()->noContent();
    }
}
