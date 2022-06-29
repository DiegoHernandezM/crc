<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['json.response']], function () {

    // public routes
    require base_path('routes/api/public/public.php');

    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });

    // private routes
    Route::middleware('auth:api')->group(function () {
        Route::get('/logout', 'Api\AuthController@logout')->name('logout');
    });
});

Route::group(
    [
        'middleware' => ['auth:api'],
        'prefix'     => '/v1'
    ],
    function () {
        Route::get('/user', 'Api\UserController@show')->name('user.show');

        require base_path('routes/api/area/area.php');

        require base_path('routes/api/subarea/subarea.php');

        require base_path('routes/api/board/board.php');

        require base_path('routes/api/associatetype/associatetype.php');

        require base_path('routes/api/associates/associates.php');

        require base_path('routes/api/shift/shift.php');

        require base_path('routes/api/reports/reports.php');

        require base_path('routes/api/checkin/checkin.php');

        require base_path('routes/api/attendance/attendance.php');

        require base_path('routes/api/hours/hours.php');
    }
);
