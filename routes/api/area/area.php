<?php
Route::get('/area/all', 'Api\AreaController@index')->name('area.all');
Route::post('/area/store', 'Api\AreaController@store')->name('area.store');
Route::get('/area/edit/{id}', 'Api\AreaController@edit')->name('area.edit');
Route::post('/area/update/{id}', 'Api\AreaController@update')->name('area.update');
Route::post('/area/destroy/{id}', 'Api\AreaController@destroy')->name('area.destroy');
Route::post('/area/restore/{id}', 'Api\AreaController@restore')->name('area.restore');
