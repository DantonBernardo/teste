<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MateriaisController;
use App\Http\Controllers\MaquinasController;
use App\Http\Controllers\PedidosController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {

    // Auth
    Route::get('/verify-token', [AuthController::class, 'verifyToken']);

    // Materiais - Apenas listar e atualizar
    Route::apiResource('materiais', MateriaisController::class)->only(['index', 'update']);

    // Máquinas - Apenas listar e atualizar
    Route::apiResource('maquinas', MaquinasController::class)->only(['index', 'update']);

    // Rotas específicas fixas de Pedidos (antes das dinâmicas)
    Route::get('/pedidos/buscar', [PedidosController::class, 'buscar']);
    Route::get('/pedidos/ultimos', [PedidosController::class, 'last']);
    Route::get('/pedidos/count', [PedidosController::class, 'count']);
    Route::get('/pedidos/pendentes', [PedidosController::class, 'pendentes']);
    Route::get('/pedidos/em-producao', [PedidosController::class, 'emProducao']);
    Route::get('/pedidos/concluidos', [PedidosController::class, 'concluidos']);
    Route::get('/pedidos/faturamento', [PedidosController::class, 'faturamento']);

    // Atualizar status específico do pedido
    Route::put('/pedidos/{pedido}/status', [PedidosController::class, 'atualizarStatus']);

    // Rotas dinâmicas CRUD de pedidos
    Route::apiResource('pedidos', PedidosController::class);

    Route::put('/pedido/editar/{id}', [PedidosController::class, 'update']);
    Route::put('/pedido/{id}/final-value', [PedidosController::class, 'changeFinalValue']);

});


Route::get('/check', function () {
    return response()->json(['status' => 'CORS FUNCIONANDO']);
});