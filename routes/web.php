<?php

use App\Controllers\AuthController;
use App\Controllers\CordonController;
use App\Controllers\DisciplineController;
use App\Controllers\FinanceController;
use App\Controllers\LogistiqueController;
use App\Controllers\ParticipantController;

// use Router\Router;

Router\Router::get('/', [AuthController::class, 'index']);
Router\Router::post('/login', [AuthController::class, 'login']);
Router\Router::get('/google-auth',[AuthController::class,'google']);
Router\Router::get('/logout', [AuthController::class, 'logout']);
Router\Router::post('/logout', [AuthController::class, 'logout']);

Router\Router::get('/register', [AuthController::class, 'showRegister']);
Router\Router::post('/register', [AuthController::class, 'register']);
Router\Router::get('/create-account', [AuthController::class, 'createAccount']);
Router\Router::get('/coordon/register', [AuthController::class, 'showRegister']);
Router\Router::post('/coordon/register', [AuthController::class, 'register']);
Router\Router::get('/forgot-password', [AuthController::class, 'forgotPassword']);
Router\Router::post('/forgot-password', [AuthController::class, 'forgotPasswordSubmit']);
Router\Router::get('/profile', [AuthController::class, 'profile']);
Router\Router::get('/settings', [AuthController::class, 'settings']);

Router\Router::get('/encadreur', [ParticipantController::class, 'index']);
Router\Router::post('/encadreur', [ParticipantController::class, 'store']);

Router\Router::get('/logistique', [LogistiqueController::class, 'index']);
Router\Router::post('/api/logistique', [LogistiqueController::class, 'handle']);

Router\Router::get('/finance', [FinanceController::class, 'index']);
Router\Router::post('/api/finance', [FinanceController::class, 'handle']);

Router\Router::get('/discipline', [DisciplineController::class, 'index']);
Router\Router::post('/api/discipline', [DisciplineController::class, 'handle']);

Router\Router::get('/cordon', [CordonController::class, 'index']);
Router\Router::get('/coordon', [CordonController::class, 'index']);
