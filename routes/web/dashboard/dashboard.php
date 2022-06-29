<?php
Route::get('/')->name('dashboard')->uses('DashboardController')->middleware('auth');
Route::get('dashboard/besthours')->name('dashboard.besthours')->uses('DashboardController@getBestExtraHours');
Route::get('dashboard/absences')->name('dashboard.absences')->uses('DashboardController@getAbsencesWeek');
Route::get('dashboard/absencesday')->name('dashboard.absencesday')->uses('DashboardController@getAbsencesByDay');
Route::get('dashboard/prodsorter')->name('dashboard.prodsorter')->uses('DashboardController@getSorterProdWeek');
Route::get('dashboard/prodpicking')->name('dashboard.prodpicking')->uses('DashboardController@getPickingProdWeek');