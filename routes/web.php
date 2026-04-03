<?php

use App\Http\Controllers\PresentationController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('presentations.index'));

Route::prefix('presentations')->name('presentations.')->group(function () {
    Route::get('/', [PresentationController::class, 'index'])->name('index');
    Route::get('/create', [PresentationController::class, 'create'])->name('create');
    Route::post('/', [PresentationController::class, 'store'])->name('store');
    Route::get('/{presentation}/builder', [PresentationController::class, 'builder'])->name('builder');
    Route::get('/{presentation}/export/pdf', [PresentationController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/{presentation}/export/pptx', [PresentationController::class, 'exportPptx'])->name('export.pptx');
    Route::get('/{presentation}/export/figma', [PresentationController::class, 'exportFigma'])->name('export.figma');
});
