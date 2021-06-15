<?php

use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\BookingController;
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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

// demo vue post crud

// Auth
Route::group([
    'middleware' => 'api',
    'prefix'     => 'auth'

], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('change', [AuthController::class, 'changePassword'])->name('change');
    Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
});

// Meeting
Route::group([
    'middleware' => ['api', 'auth', 'jwt.auth'],
    'prefix'     => 'meeting'
], function () {
    Route::get('/', [MeetingController::class, 'index'])->name('meeting');
    Route::post('create', [MeetingController::class, 'createMeeting'])->name('meeting.create');
    Route::get('show/{id}', [MeetingController::class, 'showMeeting'])->name('meeting.show');
    Route::post('update/{id}', [MeetingController::class, 'updateMeeting'])->name('meeting.update');
    Route::get('delete/{id}', [MeetingController::class, 'deleteMeeting'])->name('meeting.delete');
});

//users
Route::group([
    'middleware' => ['api', 'auth', 'jwt.auth'],
], function () {
    Route::get('users', [UserController::class, 'index'])->name('user.index');
    Route::post('user', [UserController::class, 'store'])->name('user.store');
    Route::post('user/{id}/uploadImage', [UserController::class, 'uploadImage'])->name('user.upload_image');
    Route::delete('delete/{id}', [UserController::class, 'destroy'])->name('user.destroy');
});

// Booking
Route::group([
    'middleware' => ['api','auth','jwt.auth'],
    'prefix' => 'booking'
], function () {
    Route::get('/', [BookingController::class, 'index'])->name('booking');
    Route::get('/location/{id}', [BookingController::class, 'listBooking'])->name('booking.list');
    Route::get('/create/{id}', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/create/{id}', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/listUsers', [BookingController::class, 'getListUsers'])->name('booking.listUsers');
});

//Location
Route::group([
    'middleware' => 'api',
    'prefix'     => 'location'
], function () {
    Route::get('/', [\App\Http\Controllers\LocationController::class, 'index'])->name('location');
});
