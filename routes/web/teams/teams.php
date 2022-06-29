<?php
Route::get('teams')->name('teams')->uses('TeamsController@index')->middleware('auth');
Route::get('teams/edit')->name('teams.edit')->uses('TeamsController@edit')->middleware('auth');
Route::get('teams/data')->name('teams.data')->uses('TeamsController@getAllData')->middleware('auth');