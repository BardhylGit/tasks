<?php

// /*
// |--------------------------------------------------------------------------
// | Application Routes
// |--------------------------------------------------------------------------
// |
// | This route group applies the "web" middleware group to every route
// | it contains. The "web" middleware group is defined in your HTTP
// | kernel and includes session state, CSRF protection, and more.
// |
// */

Route::get('/', function () {
    return redirect()->guest('login');
});
Route::get('/home', function () {
    return redirect()->guest('login');
});

Route::auth();

Route::group(['prefix'=> 'api', 'middleware' => ['auth']], function () {
    Route::group(['middleware' => ['roles'], 'roles'=>['admin']], function () {
        /* ADMIN ONLY ROUTES*/
        Route::post('/users/store', 'UserController@store');
        Route::delete('/users/delete', 'UserController@destroy');
        Route::put('/users/update', 'UserController@update');
    });

    Route::delete('/task/delete', 'TaskController@destroy');
    Route::post('/task/store', 'TaskController@store');
    Route::put('/task/update', 'TaskController@update');
    

});

Route::group(['middleware' => ['auth', 'roles'], 'roles'=>['admin']], function () {
    /* ADMIN ONLY ROUTES*/
    Route::get('/users/list', 'UserController@index');
    Route::get('/user/create', 'UserController@create');
    Route::get('/user/edit/{user_id}', 'UserController@edit');
    Route::get('/user/details/{user_id}', 'UserController@details');
    
    /* return user tasks list as partial view*/
    Route::get('/user/tasks/{user_id}', 'TaskController@userTasks');
});

Route::group(['middleware' => ['auth', 'roles'], 'roles'=>['admin', 'regular']], function () {
    Route::get('/tasks/list', 'TaskController@index');
    Route::get('/task/create', 'TaskController@create');
    Route::get('/task/edit/{task_id}', 'TaskController@edit');
    Route::get('/task/details/{task_id}', 'TaskController@details');
    
    /* Download task list as CSV and XML files*/
    Route::get('/task/list/{file_ext}', 'TaskController@downloadTaskList');
});

