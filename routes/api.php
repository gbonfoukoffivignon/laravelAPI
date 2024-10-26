<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;


/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/

//Route::middleware('auth:api')->group(function () {
Route::middleware('auth:api')->get('/verify-token', [UserController::class, 'verifyToken']);
Route::post("/usersregister",[UserController::class,"store"]);
Route::post("/login",[UserController::class,"login"]);
Route::post("/logout",[UserController::class,"logout"]);
Route::post("/resetPassword",[UserController::class,"resetPassword"]);
Route::get("/user/{id}",[UserController::class,"show"]);
Route::get("/users",[UserController::class,"index"]);
Route::put("/user/{id}",[UserController::class,"update"]);
Route::delete("/user/{id}",[UserController::class,"destroy"]);
Route::get('/user-id', [UserController::class, 'getUserId']);


Route::post("/register/event",[EventController::class,"store"]);
Route::get("/event/{id}",[EventController::class,"show"]);
Route::get("/events",[EventController::class,"index"]);
Route::put("/event_update/{id}",[EventController::class,"update"]);
Route::delete("/delete_event/{id}",[EventController::class,"destroy"]);
Route::get("/search/{title}",[EventController::class,"getEventByTitle"]);


Route::post("/register/reservation",[BookingController::class,"store"]);
Route::get("/reservation/{id}",[BookingController::class,"show"]);
Route::get("/reservations",[BookingController::class,"index"]);
Route::put("/reservation/{id}",[BookingController::class,"update"]);
Route::delete("/cancel-reservation/{id}",[BookingController::class,"destroy"]);
//Route::get("reservations/ofUser/",[BookingController::class,"getUserReservationsByToken"]);
Route::middleware('auth:sanctum')->get('/user/reservations/{id}', [BookingController::class, 'getUserReservationsById']);

//)};