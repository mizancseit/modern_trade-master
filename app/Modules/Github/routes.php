<?php

Route::group(
    ['module' => 'Github', 'namespace' => 'App\Modules\Github\Controllers'], function () {
		Route::get('/test_mo', 'GithubController@index');
    }
);

