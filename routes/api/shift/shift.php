<?php
Route::get('/shift/all', 'Api\ShiftController@show')->name('shift.all');
Route::post('/shift/create', 'Api\ShiftController@create')->name('shift.create');
Route::get('/shift/edit/{id}', 'Api\ShiftController@edit')->name('shift.edit');
Route::post('/shift/update/{id}', 'Api\ShiftController@update')->name('shift.update');
Route::post('/shift/destroy/{id}', 'Api\ShiftController@destroy')->name('shift.destroy');
Route::post('/shift/restore/{id}', 'Api\ShiftController@restore')->name('shift.restore');
Route::get('/shift/area/{area}', 'Api\ShiftController@showByArea')->name('shift.showByArea');
