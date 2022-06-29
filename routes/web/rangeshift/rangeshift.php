<?php

Route::post('range')->name('range')->uses('RangeShiftController@createRange')->middleware('auth');
