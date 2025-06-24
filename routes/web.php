<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return "Don't mistake humility for ignorance.";
});

require __DIR__ . '/auth.php';
