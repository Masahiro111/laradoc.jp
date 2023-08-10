<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::redirect('/installation', '/inertia/installation');
// Route::redirect('/creating-chirps', '/inertia/creating-chirps');
// Route::redirect('/showing-chirps', '/inertia/showing-chirps');
// Route::redirect('/editing-chirps', '/inertia/editing-chirps');
// Route::redirect('/deleting-chirps', '/inertia/deleting-chirps');
// Route::redirect('/notifications-and-events', '/inertia/notifications-and-events');


Route::redirect('/laravel10', '/laravel10/ja/installation');
Route::redirect('/livewire3', '/livewire3/ja/quickstart');

Route::get('/', function () {

    return redirect('/laravel10');

    // return view('docs', [
    //     'page' => 'installation',
    // ]);
});

Route::get('/{page}/{lang}/{section}', function (string $page = 'installation', string $lang = 'ja', string $section = '') {

    // dd($page . '/' .  $lang . '/' . $section);

    if (View::exists($page . '/' .  $lang . '/' . $section)) {
        return view('docs', [
            'page' => $page,
            'lang' => $lang,
            'section' => $section,
            'view_pass' => $page . '/' .  $lang . '/' . $section,
        ]);
    }

    $fallback = preg_replace('/^(inertia|blade)\//', '', $page);

    abort_unless(View::exists($fallback), 404);

    return view('docs', [
        'page' => $fallback
    ]);
})->where('page', '[a-z0-9-_\/]+');
