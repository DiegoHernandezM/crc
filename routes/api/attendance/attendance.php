<?php
Route::get('attendance/all','Api\AttendanceController@all')->name('attendance.all');
Route::get('attendance/historic','Api\AttendanceController@historic')->name('attendance.historic');
Route::get('attendance/associate/historic/{id}','Api\AttendanceController@historicAssociate')->name('attendance.historic.associate');
