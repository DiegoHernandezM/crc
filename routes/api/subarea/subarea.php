<?php
Route::get('/subarea/all', 'Api\SubareaController@show')->name('subarea.all');
Route::post('/subarea/create', 'Api\SubareaController@create')->name('subarea.create');
Route::get('/subarea/edit/{id}', 'Api\SubareaController@edit')->name('subarea.edit');
Route::post('/subarea/update/{id}', 'Api\SubareaController@update')->name('subarea.update');
Route::post('/subarea/destroy/{id}', 'Api\SubareaController@destroy')->name('subarea.destroy');
Route::post('/subarea/restore/{id}', 'Api\SubareaController@restore')->name('subarea.restore');
Route::get('/subarea/area/', 'Api\SubareaController@getFromArea')->name('subarea.getFromArea');
Route::get('/subarea/area/{area}', 'Api\SubareaController@showByArea')->name('subarea.showByArea');
// Route::get('subarea/area/{id}', 'Api\SubareaController@getSubareasByArea')->name('subarea.byarea');
