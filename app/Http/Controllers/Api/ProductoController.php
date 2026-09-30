<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(): JsonResponse
    {
        $productos = Producto::with([
            'categoria',
            'imagenes',
        ])
        ->where('activo', true)
        ->get();

        return response()->json($productos);
    }

    public function show(Producto $producto): JsonResponse
    {
        $producto->load([
            'categoria',
            'imagenes',
        ]);

        return response()->json($producto);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'categoria_id' => ['required', 'exists:categorias,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:productos,sku'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'etiqueta' => ['nullable', 'string', 'max:100'],
            'activo' => ['boolean'],
        ]);

        $producto = Producto::create($datos);

        $producto->load([
            'categoria',
            'imagenes',
        ]);

        return response()->json([
            'message' => 'Producto creado correctamente.',
            'producto' => $producto,
        ], 201);
    }

    public function update(Request $request, Producto $producto): JsonResponse
    {
        $validated = $request->validate([
            'categoria_id' => ['required', 'exists:categorias,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:productos,sku,' . $producto->id],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'etiqueta' => ['nullable', 'string', 'max:100'],
            'activo' => ['boolean'],
        ]);

        $producto->update($validated);

        return response()->json([
            'message' => 'Producto actualizado correctamente.',
            'producto' => $producto->load([
                'categoria',
                'imagenes',
            ]),
        ]);
    }

    public function destroy(Producto $producto): JsonResponse
    {
        $producto->update([
            'activo' => false,
        ]);

        return response()->json([
            'message' => 'Producto eliminado correctamente.',
        ]);
    }
}