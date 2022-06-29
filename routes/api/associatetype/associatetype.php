<?php
Route::get('/associatetype/all', 'Api\AssociateTypeController@index')->name('associatetype.all');
Route::post('/associatetype/store', 'Api\AssociateTypeController@store')->name('associatetype.store');
Route::get('/associatetype/edit/{id}', 'Api\AssociateTypeController@edit')->name('associatetype.edit');
Route::post('/associatetype/update/{id}', 'Api\AssociateTypeController@update')->name('associatetype.update');
Route::post('/associatetype/destroy/{id}', 'Api\AssociateTypeController@destroy')->name('associatetype.destroy');
Route::post('/associatetype/restore/{id}', 'Api\AssociateTypeController@restore')->name('associatetype.restore');
