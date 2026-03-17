<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
// All other routes go to Vue app (admin panel)
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!mobile).*$');  // Exclude any routes starting with 'mobile'
