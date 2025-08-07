<?php

use Illuminate\Support\Facades\Route;

// Test route to see if routing works
Route::get('/test-image', function () {
    return response('Image route works!');
}); 