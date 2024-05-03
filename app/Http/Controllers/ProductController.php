<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() : View
    {
        $products = Product::latest()->paginate(10);

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() : View
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) : RedirectResponse
    {
        $request->validate([
            'image'     => 'required|image|max:2048',
            'title'     => 'required|min:5',
            'desc'      => 'required|min:10',
            'price'     => 'required|numeric',
            'stock'     => 'required|numeric'
        ]);

        $image = $request->file('image');
        $image->storeAs('public/products/', $image->hashName());

        Product::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'desc'      => $request->desc,
            'price'     => $request->price,
            'stock'     => $request->stock
        ]);

        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id) : View
    {
        $product = Product::findOrFail($id);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id) : View
    {
        $product = Product::findOrFail($id);

        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) : RedirectResponse
    {
        $request->validate([
            'image'     => 'image|max:2048',
            'title'     => 'required|min:5',
            'desc'      => 'required|min:10',
            'price'     => 'required|numeric',
            'stock'     => 'required|numeric'
        ]);

        $product = Product::findOrFail($id);
        
        if($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/products/', $image->hashName());

            Storage::delete('public/products/'. $product->image);

            $product->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'desc'     => $request->desc,
                'price'     => $request->price,
                'stock'     => $request->stock,
            ]);
        } else { 
            $product->update([
                'title'     => $request->title,
                'desc'     => $request->desc,
                'price'     => $request->price,
                'stock'     => $request->stock,
            ]);
        }

        return redirect()->route('products.index')->with(['success', 'Data berhasil di update.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) : RedirectResponse
    {
        $product = Product::findOrFail($id);

        Storage::delete('public/products/'. $product->image);
        $product->delete();

        return redirect()->route('products.index')->with(['success', 'Data berhasil di hapus.']);
    }
}
