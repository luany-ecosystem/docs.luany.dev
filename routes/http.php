<?php

use App\Controllers\DocsController;
use Luany\Core\Routing\Route;

Route::get('/', [DocsController::class, 'home']);
Route::get('/{locale}', [DocsController::class, 'landing']);
Route::get('/{locale}/docs', [DocsController::class, 'index']);
Route::get('/{locale}/docs/{slug}', [DocsController::class, 'show'])->name('docs.show');
Route::get('/{locale}/search.json', [DocsController::class, 'search']);
