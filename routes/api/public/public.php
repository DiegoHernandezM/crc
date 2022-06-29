<?php
Route::post('/login', 'Api\AuthController@login')->name('login.api');
Route::get('/verify', 'Api\AuthController@verifyemail')->name('user.verify');
