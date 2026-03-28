<?php

use App\Models\Invoice;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/admin');

Route::middleware(['auth'])->group(function () {
    Route::get('/invoice/{invoice}/pdf', function (Invoice $invoice) {
        return $invoice->streamPdf();
    })->name('invoice.pdf');

    Route::get('/invoice/{invoice}/pdf/download', function (Invoice $invoice) {
        $pdf = $invoice->generatePdf();
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    })->name('invoice.pdf.download');

    Route::get('/invoice/{invoice}/print', function (Invoice $invoice) {
        return view('invoices.print', compact('invoice'));
    })->name('invoice.print');
});


Route::get('/migrate', function () {
    Artisan::call('migrate');
    return 'migrated!';
});

Route::get('/migrate:fresh', function () {
    Artisan::call('migrate');
    return 'migrated!';
});


Route::get('/optimize-clear', function () {
    Artisan::call('optimize:clear');
    return 'cache cleared successfully!';
});

Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return 'storage linked successfully!';
});