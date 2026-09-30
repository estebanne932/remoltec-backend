<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriaController extends Controller
{
    // Listar categorías
    public function index(): JsonResponse
    {
        $categorias = Categoria::withCount('productos')
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        return response()->json($categorias);
    }

    // Mostrar una categoría
    public function show(Categoria $categoria): JsonResponse
    {
        $categoria->load([
            'productos' => function ($query) {
                $query->where('activo', true)
                    ->orderBy('nombre');
            }
        ]);

        return response()->json($categoria);
    }

    // Productos de una categoría
    public function productos(Categoria $categoria): JsonResponse
    {
        $productos = $categoria->productos()
            ->where('activo', true)
            ->with([
                'categoria',
                'imagenes',
            ])
            ->orderBy('nombre')
            ->get();

        return response()->json($productos);
    }

    // Crear categoría
    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'imagen' => ['nullable', 'string', 'max:255'],
            'activo' => ['boolean'],
            'orden' => ['integer'],
        ]);

        $datos['slug'] = Str::slug($datos['nombre']);

        $categoria = Categoria::create($datos);

        return response()->json([
            'message' => 'Categoría creada correctamente.',
            'categoria' => $categoria,
        ], 201);
    }

    // Actualizar categoría
    public function update(
        Request $request,
        Categoria $categoria
    ): JsonResponse {

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'imagen' => ['nullable', 'string', 'max:255'],
            'activo' => ['boolean'],
            'orden' => ['integer'],
        ]);

        $datos['slug'] = Str::slug($datos['nombre']);

        $categoria->update($datos);

        return response()->json([
            'message' => 'Categoría actualizada correctamente.',
            'categoria' => $categoria,
        ]);
    }

    // Eliminar categoría
    public function destroy(Categoria $categoria): JsonResponse
    {
        if ($categoria->productos()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar una categoría que tiene productos.'
            ], 422);
        }

        $categoria->delete();

        return response()->json([
            'message' => 'Categoría eliminada correctamente.'
        ]);
    }
}