<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;


Route::get('/', function () {
    return file_get_contents(public_path('index.html'));
});


Route::get('/{any}', function () {
    return file_get_contents(public_path('index.html'));
})->where('any', '.*');