<?php

use App\Http\Controllers\Api\V1\ContactController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // お問い合わせ一覧画面を表示
    Route::get('/contacts', [ContactController::class, 'index']);
    // お問い合わせ詳細画面表示
    Route::get('/contacts/{contact}', [ContactController::class, 'show']);
    // お問い合わせ新規作成
    Route::post('/contacts', [ContactController::class, 'store']);
    // お問い合わせ更新
    Route::put('/contacts/{contact}', [ContactController::class, 'update']);
    // お問い合わせ削除
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy']);
});
