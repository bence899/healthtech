<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/upload', function (Request $request) {
    $request->file('file')->store('uploads', 's3');
    return 'Upload successful';
});
    