<?php
Route::get('checkin/all', 'Api\CheckinController@index')->name('checkin.all');
Route::get('checkin/associate/{id}', 'Api\CheckinController@getAssistsAssociate')->name('checkin.associate');
Route::post('checkin/check', 'Api\CheckinController@checkAssociate')->name('checkin.check');
Route::post('checkin/update/{id}', 'Api\CheckinController@update')->name('checkin.update');
Route::post('checkin/store', 'Api\CheckinController@store')->name('checkin.store');
