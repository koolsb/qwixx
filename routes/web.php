<?php

use App\Services\LayoutLibrary;
use Illuminate\Support\Facades\Route;

Route::get('/', function (LayoutLibrary $library) {
    return view('picker', ['layouts' => $library->all()]);
})->name('picker');

Route::get('/play/{layout}/{mode?}', function (LayoutLibrary $library, string $layout, string $mode = 'solo') {
    abort_unless(in_array($mode, ['solo', 'duo'], true), 404);

    $layout = $library->find($layout) ?? abort(404);

    return view('game', ['layout' => $layout, 'mode' => $mode]);
})->name('game');
