<?php

namespace App\Services;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventarioService
{
    public function entrada(
        Producto $producto,
        int $cantidad,
        ?string $motivo = null
    ): MovimientoInventario {

        return DB::transaction(function () use (
            $producto,
            $cantidad,
            $motivo
        ) {

            if ($cantidad <= 0) {
                throw new RuntimeException(
                    'La cantidad debe ser mayor a cero.'
                );
            }

            $producto->increment('stock', $cantidad);

            return MovimientoInventario::create([
                'producto_id' => $producto->id,
                'tipo' => 'entrada',
                'cantidad' => $cantidad,
                'motivo' => $motivo,
                'pedido_id' => null,
            ]);
        });
    }

    public function salida(
        Producto $producto,
        int $cantidad,
        ?int $pedidoId = null,
        ?string $motivo = null
    ): MovimientoInventario {

        return DB::transaction(function () use (
            $producto,
            $cantidad,
            $pedidoId,
            $motivo
        ) {

            if ($cantidad <= 0) {
                throw new RuntimeException(
                    'La cantidad debe ser mayor a cero.'
                );
            }

            if ($producto->stock < $cantidad) {
                throw new RuntimeException(
                    'Stock insuficiente para el producto.'
                );
            }

            $producto->decrement('stock', $cantidad);

            return MovimientoInventario::create([
                'producto_id' => $producto->id,
                'tipo' => 'salida',
                'cantidad' => $cantidad,
                'motivo' => $motivo,
                'pedido_id' => $pedidoId,
            ]);
        });
    }

    public function ajustar(
        Producto $producto,
        int $nuevoStock,
        ?string $motivo = null
    ): MovimientoInventario {

        return DB::transaction(function () use (
            $producto,
            $nuevoStock,
            $motivo
        ) {

            if ($nuevoStock < 0) {
                throw new RuntimeException(
                    'El stock no puede ser negativo.'
                );
            }

            $diferencia = $nuevoStock - $producto->stock;

            $producto->update([
                'stock' => $nuevoStock,
            ]);

            return MovimientoInventario::create([
                'producto_id' => $producto->id,
                'tipo' => 'ajuste',
                'cantidad' => $diferencia,
                'motivo' => $motivo,
                'pedido_id' => null,
            ]);
        });
    }
}