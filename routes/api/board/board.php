<?php
Route::get('board/all', 'Api\BoardController@index')->name('board.all');
Route::post('board/store', 'Api\BoardController@store')->name('board.store');
Route::get('board/edit/{id}', 'Api\BoardController@edit')->name('board.edit');
Route::post('board/update/{id}', 'Api\BoardController@update')->name('board.update');
Route::get('board/{id}', 'Api\BoardController@destroy')->name('board.destroy');
