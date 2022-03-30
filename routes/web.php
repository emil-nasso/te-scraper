<?php

use App\Models\Tea;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Facades\Image;

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

Route::get('/', function () {
    return view('teas');
});

Route::get('/img/{teaId}', function ($teaId) {
    // Cache::forget("image.{$teaId}");
    $response = Cache::remember("image.{$teaId}", CarbonInterval::hour(), function () use ($teaId) {
        $response = Http::get(Tea::find($teaId)->imageUrl);

        return [
            'headers' => $response->headers(),
            'data' => (string) Image::make((string) $response->body())->fit(256)->stream(),
        ];
    });

    return response()->streamDownload(
        function () use ($response) {
            echo $response['data'];
        },
        null,
        [
            'Content-Type' => $response['headers']['Content-Type'],
            'Cache-Control' => 'max-age=3600',
        ],
        'inline'
    );
})->name('tea.image');
