<?php

use App\Http\Controllers\ContactController;
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
// 管理画面表示
// Route::get('/admin', )
