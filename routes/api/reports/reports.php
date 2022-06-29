<?php

Route::get('reports/shift')->name('reports.shift')->uses('Api\ReportsController@shiftAssociate')->middleware('auth');
Route::get('reports/associate/historic')->name('reports.historic')->uses('Api\ReportsController@getAssociateRegisters');
Route::get('reports/historic')->name('reports.generalhistoric')->uses('Api\ReportsController@getReportAssociates');
Route::get('reports/extrahours')->name('reports.extrahours')->uses('Api\ReportsController@getReportExtraHours')->middleware('auth');
Route::get('reports/dataextrahours', 'Api\ReportsController@getDataExtraHours')->name('reports.dataextrahours');
Route::get('reports/exportextrahours')->name('reports.exportextrahours')->uses('Api\ReportsController@getExcelExtraHours');

//Picking
Route::get('reports/picking')->name('reports.picking')->uses('Api\ReportsController@getPicking')->middleware('auth');
Route::get('reports/exportpickingbonus')->name('reports.exportpickingbonus')->uses('Api\ReportsController@getExcelPickingBonus');
Route::get('reports/datapickingbonus', 'Api\ReportsController@getDataPickingBonus')->name('reports.datapickingbonus');
Route::post('reports/loadpickingproductivity')->name('reports.loadpickingproductivity')->uses('Api\ReportsController@loadPickingProductivity');


//Sorter
Route::get('reports/sorter')->name('reports.sorter')->uses('SorterReportController@productivitySorter');
Route::post('reports/sorter/datawamas')->name('reports.datawamas')->uses('SorterReportController@dataExcelWamas');
Route::post('reports/sorter/stops')->name('reports.stops')->uses('SorterReportController@subtractStopsSorter');
Route::post('reports/sorter/calculatetimes')->name('reports.times')->uses('SorterReportController@calculateTimesSorter');
Route::post('reports/calculatebonussorter')->name('reports.calculatebonussorter')->uses('SorterReportController@calculateBonusSorter');
Route::get('reports/calculateprodsorter')->name('reports.calculateprodsorter')->uses('SorterReportController@calculateBonusSorter');
Route::get('reports/getbonusdata')->name('reports.getbonusdata')->uses('SorterReportController@getDataBonus');
Route::get('reports/exportsorterbonus')->name('reports.exportsorterbonus')->uses('SorterReportController@getReportBonus');
Route::get('reports/getpendingprods')->name('reports.getpendingprods')->uses('SorterReportController@getPending');
Route::get('reports/calculatebonusstaffsorter')->name('reports.calculatebonusstaffsorter')->uses('SorterReportController@calculateBonusStaffSorter');
Route::get('reports/getbonusstaff')->name('reports.getbonusstaff')->uses('SorterReportController@getBonusStaffSorter');
Route::get('reports/exportsorterstaffbonus')->name('reports.exportsorterstaffbonus')->uses('SorterReportController@getReportStaffManagerBonus');
