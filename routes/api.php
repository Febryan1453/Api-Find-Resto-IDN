<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RestoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [AuthController::class, 'masuk']);
Route::post('/regis', [AuthController::class, 'daftar']);

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('/logout', [AuthController::class, 'keluar']);

    // CRUD Resto sekaligus menunya
    Route::post('/add/resto-dan-menu', [RestoController::class, 'createRestoMenu']);
    Route::get('/resto/{id}', [RestoController::class, 'getRestoMenu']);
    Route::put('/update/resto-dan-menu/{id}', [RestoController::class, 'putRestoMenu']);

    //CRUD Menu pada restoran berdasarkan ID Restoran
    Route::post('/add/menu/{resto_id}', [RestoController::class, 'createMenu']);
    Route::put('/update/menu/{resto_id}/{menu_id}', [RestoController::class, 'putMenu']);
    Route::delete('/delete/menu/{resto_id}/{menu_id}', [RestoController::class, 'deleteMenu']);

    //Update Restonya saja
    Route::put('/update/resto/{resto_id}', [RestoController::class, 'putResto']);

    //GET Semua menu
    Route::get('/menu', [RestoController::class, 'getMenu']);
});

/*
|--------------------------------------------------------------------------
| Fitur pada API ini adalah sebagai berikut :
|--------------------------------------------------------------------------
|
| 1.  Registrasi
| 2.  Login (dapatkan token untuk semua action setelah login)
| 3.  ADD Resto sekaligus dengan menunya
| 4.  Get Resto secara tunggal beserta menu pada resto tersebut
| 5.  PUT Resto dan menunya sekaligus
| 6.  ADD Menu pada resto tanpa PUT pada tabel data restonya
| 7.  PUT Menu pada resto tanpa PUT pada tabel data restonya
| 8.  DEL Menu pada resto tanpa PUT pada tabel data restonya
| 9.  PUT pada tabel data resto saja
| 10. GET data menu pada tabel data menu
| 11. Logout (akan menghapus session/atau token login pada akun yang terdaftar)
|
|--------------------------------------------------------------------------
| Untuk pemakaian secara HYBRID yaitu pada
| Aplikasi Android, Flutter dan Website
|
| Silahkan digunakan dan didistribusikan seluas-luasnya dengan syarat
| Tolong JANGAN HAPUS PESAN HEADER INI !!!
|
| Untuk kerjasama hubungi :
| WhatsApp : 082191170349
| Email    : ryan@idnfoundation.org
| Github   : Febryan1453
| IG       : fbrynryn
|--------------------------------------------------------------------------
*/
