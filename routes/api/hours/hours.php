<?php
Route::get('hours/data', 'Api\ExtraHoursController@getHours')->name('reports.data');
//Route::get('reports/exportextrahours')->name('reports.exportextrahours')->uses('ReportsController@getExcelExtraHours');
