<?php

use App\Http\Controllers\Api\V1\AnswerController;
use App\Http\Controllers\Api\V1\QuizController;
use App\Http\Controllers\Api\V1\UserController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'], function() {
    Route::post('/user/register', [UserController::class, 'createUser']);
    Route::post('/user/login', [UserController::class, 'loginUser']);
    Route::post('/forgot-password',[UserController::class, 'forgotPassword'])->middleware('guest');
    Route::post('/rc/apple-server-notifications', [UserController::class, 'processAppleServerNotification']);
    // Route::get('/gtest', [UserController::class, 'getTest']);
    // Route::post('/ptest', [UserController::class, 'postTest']);
});
Route::middleware('jwt.verify')->get('/authtest', function(Request $request) {
    try {
        return response()->json([
            'status' => true,
            'user' => $request->user(),
            'request' => $_REQUEST,
            'get' => $_GET,
            'post' => $_POST,
            'server' => $_SERVER
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage(),
            'request' => $_REQUEST,
            'get' => $_GET,
            'post' => $_POST,
            'server' => $_SERVER
        ], 500);
    }
});

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1', 'middleware' => 'jwt.verify'], function() {
    Route::get('/gtest_auth', [UserController::class, 'getTest']);
    Route::post('/ptest_auth', [UserController::class, 'postTest']);
    Route::get('/user/current', [UserController::class, 'currentUser']);
    Route::post('/user/update', [UserController::class, 'updateUser']);
    Route::post('/user/logout', [UserController::class, 'logoutUser']);
    Route::post('/user/delete', [UserController::class, 'deleteAccount']);
    Route::get('/quiz/daily', [QuizController::class, 'getDailyQuestion']);
    Route::post('/quiz/questions', [QuizController::class, 'getQuestions']);
    Route::post('/quiz/bookmark', [QuizController::class, 'bookmarkQuestion']);
    Route::get('/quiz/bookmarks', [QuizController::class, 'bookmarkedQuestions']);
    Route::post('/answer/add', [AnswerController::class, 'addAnswer']);
    Route::post('/answer/adds', [AnswerController::class, 'addAnswer1']);
    Route::get('/quiz/monthly_results', [AnswerController::class, 'getMonthlyDailyTestResults']);
    Route::get('/user/get_perf_anal_data', [AnswerController::class, 'getPeroformanceAnalysisData']);
    Route::get('/user/get_missed_questions', [AnswerController::class, 'getMissedQuestions']);
    Route::post('/quiz/questions_by_ids', [QuizController::class, 'getQuestionsByIds']);
    // Route::apiResource('customers', CustomersController:class);
    // Route::apiResource('invoices', InvoicesController:class);
});