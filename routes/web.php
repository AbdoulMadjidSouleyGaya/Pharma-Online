<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\AuthController;



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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/pharmacies', [PharmacyController::class, 'index']);

/*Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
*/
Route::get('/contact', function () {
    return view('contact');
});
Route::post('/contact/submit', [ContactController::class, 'submitForm'])->name('contact.submit');
//Route::get('/pharmacies', [PharmacyController::class, 'index']);
//Route::get('/pharmacies-de-garde', [PharmacyController::class, 'show']);
//Route::get('/pharmacies-de-garde', [PharmacyController::class, 'show']);
Route::get('/pharmacies-de-garde', [PharmacyController::class, 'index']); // ou 'show' selon votre choix
Route::get('/test', function () {
    return view('test');
});
Route::get('/choose-account', function () {
    return view('choose-account');
});
// Afficher le formulaire d'inscription
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');

// Soumettre le formulaire d'inscription
Route::post('/register', [AuthController::class, 'register']);
Route::get('/registration-success', function () {
    return view('registration-success');
})->name('registration.success');
