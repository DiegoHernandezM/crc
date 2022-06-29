<?php

Route::get('associates', 'Api\AssociateController@index')->name('associates')->middleware('remember', 'auth');
Route::post('associates/store', 'Api\AssociateController@store')->name('associates.store');
Route::get('associates/employee', 'Api\AssociateController@employee')->name('associates.employee');
Route::get('associates/{id}/edit', 'Api\AssociateController@edit')->name('associates.edit');
Route::patch('associates/{associate}', 'Api\AssociateController@update')->name('associates.update');
Route::get('associates/{id}', 'Api\AssociateController@show')->name('associates.show');
Route::delete('associates/{id}', 'Api\AssociateController@destroy')->name('associates.destroy');
Route::get('associatelist', 'Api\AssociateController@getAssociates')->name('associates.getall');
Route::post('associatelist/moveteam', 'Api\AssociateController@moveTeam')->name('associates.moveteam');
Route::post('associates/subarea/{id}', 'Api\AssociateController@updateSubarea')->name('associates.ubdatesubarea');
Route::post('associates/restore/{id}', 'Api\AssociateController@restore')->name('associates.restore');
