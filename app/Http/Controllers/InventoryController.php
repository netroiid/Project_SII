<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flower;

class InventoryController extends Controller
{
    public function index()
    {
        $flowers = Flower::all();
        return view('inventory.index', compact('flowers'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        Flower::create([
            'name'            => $request->name,
            'kategori'        => $request->kategori,
            'stock_now'       => $request->stock_now,
            'total_used'      => 0,
            'price_per_unit'  => $request->price_per_unit,
            'expired_at'      => $request->expired_at,
        ]);
        return redirect()->route('inventory.index');
    }

    public function edit(Flower $flower)
    {
        return view('inventory.edit', compact('flower'));
    }

    public function update(Request $request, Flower $flower)
    {
        $flower->update([
            'name'            => $request->name,
            'kategori'        => $request->kategori,
            'stock_now'       => $request->stock_now,
            'price_per_unit'  => $request->price_per_unit,
            'expired_at'      => $request->expired_at,
        ]);
        return redirect()->route('inventory.index');
    }

    public function destroy(Flower $flower)
    {
        $flower->delete();
        return redirect()->route('inventory.index');
    }
}

