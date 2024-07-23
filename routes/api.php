<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\TypeController;
use App\Http\Controllers\Api\ManyToMany\HotelServiceController;
use App\Http\Controllers\Api\ManyToMany\GuestServiceController;
use App\Http\Controllers\Api\ManyToMany\RoomGuestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas recurso para la API
Route::apiResource('hotel', HotelController::class);
Route::apiResource('room', RoomController::class);
Route::apiResource('guest', GuestController::class);
Route::apiResource('category', CategoryController::class);
Route::apiResource('type', TypeController::class);
Route::apiResource('service', ServiceController::class);

// Rutas recurso para la API ManyToMany
Route::apiResource('hotel-service', HotelServiceController::class)->except(['store', 'update']);
Route::post('/hotel-services/add-hotels', [HotelServiceController::class, 'storeHotels']);
Route::post('/hotel-services/add-services', [HotelServiceController::class, 'storeServices']);
Route::put('/hotel-services/update-hotels', [HotelServiceController::class, 'updateHotels']);
Route::put('/hotel-services/update-services', [HotelServiceController::class, 'updateServices']);

Route::apiResource('guest-service', GuestServiceController::class)->except(['store', 'update']);
Route::post('/guest-services/add-guests', [GuestServiceController::class, 'storeGuests']);
Route::post('/guest-services/add-services', [GuestServiceController::class, 'storeServices']);
Route::put('/guest-services/update-guests', [GuestServiceController::class, 'updateGuests']);
Route::put('/guest-services/update-services', [GuestServiceController::class, 'updateServices']);

Route::apiResource('room-guest', RoomGuestController::class)->except(['store', 'update']);
Route::post('/room-guests/{guest}/add-rooms', [RoomGuestController::class, 'storeRooms']);
Route::post('/room-guests/{room}/add-guests', [RoomGuestController::class, 'storeGuests']);
Route::put('/room-guests/{guest}/update-rooms', [RoomGuestController::class, 'updateRooms']);
Route::put('/room-guests/{room}/update-guests', [RoomGuestController::class, 'updateGuests']);

// Rutas de servicios
Route::get('service/search/{name}', [ServiceController::class, 'search']);
Route::get('service/{service}/category', [ServiceController::class, 'getCategory']);
Route::get('service/{service}/hotels', [ServiceController::class, 'getHotels']);
Route::post('service/{service}/hotels', [ServiceController::class, 'addHotels']);
Route::delete('service/{service}/hotels', [ServiceController::class, 'removeHotels']);
Route::get('service/{service}/guests', [ServiceController::class, 'getGuests']);
Route::post('service/{service}/guests', [ServiceController::class, 'addGuests']);
Route::delete('service/{service}/guests', [ServiceController::class, 'removeGuests']);

// Rutas de hoteles
Route::get('hotel/search/{name}', [HotelController::class, 'search']);
Route::get('hotel/{hotel}/rooms', [HotelController::class, 'getRooms']);
Route::get('hotel/{hotel}/services', [HotelController::class, 'getServices']);
Route::post('hotel/{hotel}/services', [HotelController::class, 'addServices']);
Route::delete('hotel/{hotel}/services', [HotelController::class, 'removeServices']);

// Rutas de habitaciones
Route::get('room/search/{number}', [RoomController::class, 'search']);
Route::get('room/{room}/hotel', [RoomController::class, 'getHotel']);
Route::get('room/{room}/type', [RoomController::class, 'getType']);
Route::get('room/{room}/guests', [RoomController::class, 'getGuests']);
Route::post('room/{room}/guests', [RoomController::class, 'addGuests']);
Route::delete('room/{room}/guests', [RoomController::class, 'removeGuests']);

// Rutas de tipos
Route::get('type/search/{name}', [TypeController::class, 'search']);
Route::get('type/{type}/rooms', [TypeController::class, 'getRooms']);

// Rutas de categorias
Route::get('category/search/{name}', [CategoryController::class, 'search']);
Route::get('category/{category}/services', [CategoryController::class, 'getServices']);

// Rutas de huespedes
Route::get('guest/search/{name}', [GuestController::class, 'search']);
Route::get('guest/{guest}/rooms', [GuestController::class, 'getRooms']);
Route::post('guest/{guest}/rooms', [GuestController::class, 'addRooms']);
Route::delete('guest/{guest}/rooms', [GuestController::class, 'removeRooms']);
Route::get('guest/{guest}/services', [GuestController::class, 'getServices']);
Route::post('guest/{guest}/services', [GuestController::class, 'addServices']);
Route::delete('guest/{guest}/services', [GuestController::class, 'removeServices']);



// Ruta de fallback para manejar rutas no encontradas
Route::fallback(function () {
    return response()->json([
        'message' => 'This route is not found or does not exist'
    ], 404);
});