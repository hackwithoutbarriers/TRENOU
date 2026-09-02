<?php

use App\Http\Controllers\PdfController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/services', [PublicController::class, 'services'])->name('services');
Route::get('/services/{slug}', [PublicController::class, 'service'])->where('slug', '[a-z0-9-]+')->name('services.detail');
Route::get('/galerie', [PublicController::class, 'gallery'])->name('gallery');
Route::get('/devis', [PublicController::class, 'showQuoteForm'])->name('public.devis');
Route::post('/devis', [PublicController::class, 'storeQuote'])->name('public.devis.store');
Route::post('/api/devis', [PublicController::class, 'storeQuoteApi'])->name('api.devis.store');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicController::class, 'storeContact'])->name('contact.store');

Route::get('/devis/{devis}/pdf', [PdfController::class, 'devis'])->name('devis.pdf');
Route::get('/attestations/{attestation}/pdf', [PdfController::class, 'attestation'])->name('attestation.pdf');
