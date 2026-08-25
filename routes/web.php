<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

// お問い合わせフォーム入力ページ
Route::get('/', [ContactController::class, 'index']);
// お問い合わせフォーム確認ページ
Route::post('/contacts/confirm', [ContactController::class, 'confirm']);
// お問い合わせフォーム内容を登録
Route::post('/contacts', [ContactController::class, 'store']);
// サンクスページ
Route::get('/thanks', [ContactController::class, 'thanks']);

