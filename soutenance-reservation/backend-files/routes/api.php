<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\JuryController;
use App\Http\Controllers\CreneauController;
use App\Http\Controllers\EmplacementController;

Route::apiResource('etudiants', EtudiantController::class);
Route::apiResource('reservations', ReservationController::class);
Route::apiResource('salles', SalleController::class);
Route::apiResource('jurys', JuryController::class);
Route::apiResource('creneaux', CreneauController::class);
Route::apiResource('emplacements', EmplacementController::class);