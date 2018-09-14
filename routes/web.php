<?php

Route::get('/', function () {
    return view('welcome');
});

Route::view('scan', 'scan');

Route::get('/threads/search', 'SearchController@show');
Route::get('/threads', 'ThreadsController@index')->name('threads');
Route::post('/threads', 'ThreadsController@store')->middleware('must-be-confirmed');
Route::get('/threads/create', 'ThreadsController@create');
Route::patch('/threads/{channel}/{thread}', 'ThreadsController@update');
Route::get('/threads/{channel}', 'ThreadsController@index');
Route::get('/threads/{channel}/{thread}', 'ThreadsController@show')->name('threads.show');
Route::delete('/threads/{channel}/{thread}', 'ThreadsController@destroy');

Route::post('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@store');
Route::delete('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@destroy');

Route::post('/replies/{reply}/best', 'BestRepliesController@store')->name('best-replies.store');

Route::post('/locked_thread/{thread}', 'LockedThreadsController@store')->name('lock-thread.store')->middleware('admin');

Route::patch('/replies/{reply}', 'RepliesController@update');
Route::post('/threads/{channel}/{thread}/replies', 'RepliesController@store');
Route::delete('/replies/{reply}', 'RepliesController@destroy')->name('replies.destroy');

Route::post('/replies/{reply}/favorites', 'FavoritesController@store');
Route::delete('/replies/{reply}/favorites', 'FavoritesController@destroy');

Route::get('/profiles/{user}', 'ProfilesController@show')->name('profile');

Route::delete('/profiles/{user}/notifications/{notification}', 'UserNotificationsController@destroy');
Route::get('/profiles/{user}/notifications', 'UserNotificationsController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('api/users', 'Api\UsersController@index');

Route::post('api/users/{user}/avatar', 'Api\UserAvatarController@store')->middleware('auth')->name('avatar');

Route::get('/register/confirm', 'Auth\RegisterConfirmationController@index');
