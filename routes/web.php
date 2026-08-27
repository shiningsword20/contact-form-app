<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// ユーザー側
// お問い合わせフォーム入力ページ表示
Route::get('/', [ContactController::class, 'index']);
// お問い合わせフォーム確認ページ表示
Route::post('/contacts/confirm', [ContactController::class, 'confirm']);
// お問い合わせフォーム内容を登録
Route::post('/contacts', [ContactController::class, 'store']);
// サンクスページhy王子
Route::get('/thanks', [ContactController::class, 'thanks']);

// 管理者側
    Route::middleware('auth')->group(function () {
        // 管理画面表示
        Route::get('/admin', [AdminController::class, 'index']);
        // お問い合わせ詳細画面表示
        Route::get('/admin/contacts/{contact}', [AdminController::class, 'show']);
        // お問い合わせ内容削除
        Route::delete('/admin/contacts/{contact}', [AdminController::class, 'destroy']);
        // タグの追加
        Route::post('/admin/tags', [TagController::class, 'store']);
        // タグの編集
        Route::get('/admin/tags/{tag}/edit', [TagController::class, 'edit']);
        // タグの更新
        Route::put('/admin/tags/{tag}', [TagController::class, 'update']);
        // タグの削除
        Route::delete('/admin/tags/{tag}', [TagController::class, 'destroy']);
});

