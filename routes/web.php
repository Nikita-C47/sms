<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Маршруты, доступные всем пользователям

// Анонимное событие
Route::get('/events/anonymous/create', 'EventsController@createAnonymous')->name('create-anonymous-event');
Route::post('/events/anonymous/create', 'EventsController@storeAnonymous');

// Маршруты авторизации
Auth::routes(['verify' => false, 'register' => false]);

// Маршруты, доступные только менеджерам событий
Route::middleware(['auth', 'role:manager'])->group(function () {
    // Категории событий
    Route::get('/admin/event-categories', 'EventCategoriesController@index')->name('event-categories');
    Route::post('/admin/event-categories/get-for-department', 'EventCategoriesController@getForDepartment')->name('categories-for-department');
    Route::get('/admin/event-categories/create', 'EventCategoriesController@create')->name('create-event-category');
    Route::post('/admin/event-categories/create', 'EventCategoriesController@store');
    Route::get('/admin/event-categories/{id?}/edit', 'EventCategoriesController@edit')->name('edit-event-category');
    Route::post('/admin/event-categories/{id?}/edit', 'EventCategoriesController@update');
    Route::post('/admin/event-categories/{id?}/delete', 'EventCategoriesController@destroy')->name('delete-event-category');

    // Обработка событий
    Route::get('/events/needs-approval', 'EventsController@indexNeedsApproval')->name('events-needs-approval');
    Route::get('/events/not-approved', 'EventsController@indexNotApproved')->name('events-not-approved');
    Route::post('/events/{id}/process', 'EventsController@processEvent')->name('event-process');
    Route::post('/events/{id?}/delete', 'EventsController@destroy')->name('delete-event');
});

// Маршруты, доступные только администратору
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Подразделения
    Route::get('/admin/departments', 'Dictionaries\DepartmentsController@index')->name('departments');
    Route::get('/admin/departments/create', 'Dictionaries\DepartmentsController@create')->name('create-department');
    Route::post('/admin/departments/create', 'Dictionaries\DepartmentsController@store');
    Route::get('/admin/departments/{id?}/edit', 'Dictionaries\DepartmentsController@edit')->name('edit-department');
    Route::post('/admin/departments/{id?}/edit', 'Dictionaries\DepartmentsController@update');
    Route::post('/admin/departments/{id?}/delete', 'Dictionaries\DepartmentsController@destroy')->name('delete-department');
    // Типы событий
    Route::get('/admin/event-types', 'Dictionaries\EventTypesController@index')->name('event-types');
    Route::get('/admin/event-types/create', 'Dictionaries\EventTypesController@create')->name('create-event-type');
    Route::post('/admin/event-types/create', 'Dictionaries\EventTypesController@store');
    Route::get('/admin/event-types/{id?}/edit', 'Dictionaries\EventTypesController@edit')->name('edit-event-type');
    Route::post('/admin/event-types/{id?}/edit', 'Dictionaries\EventTypesController@update');
    Route::post('/admin/event-types/{id?}/delete', 'Dictionaries\EventTypesController@destroy')->name('delete-event-type');
    // Мероприятия, к которым относятся события
    Route::get('/admin/event-relations', 'Dictionaries\EventRelationsController@index')->name('event-relations');
    Route::get('/admin/event-relations/create', 'Dictionaries\EventRelationsController@create')->name('create-event-relation');
    Route::post('/admin/event-relations/create', 'Dictionaries\EventRelationsController@store');
    Route::get('/admin/event-relations/{id?}/edit', 'Dictionaries\EventRelationsController@edit')->name('edit-event-relation');
    Route::post('/admin/event-relations/{id?}/edit', 'Dictionaries\EventRelationsController@update');
    Route::post('/admin/event-relations/{id?}/delete', 'Dictionaries\EventRelationsController@destroy')->name('delete-event-relation');
    // Рейсы
    Route::get('/admin/flights', 'FlightsController@index')->name('flights');
    // Пользователи
    Route::get('/admin/users', 'UsersController@index')->name('users');
    Route::get('/admin/users/create', 'UsersController@create')->name('create-user');
    Route::post('/admin/users/create', 'UsersController@store');
    Route::get('/admin/users/{id?}/edit', 'UsersController@edit')->name('edit-user');
    Route::post('/admin/users/{id?}/edit', 'UsersController@update');
    Route::post('/admin/users/{id?}/delete', 'UsersController@destroy')->name('delete-user');
    Route::post('/admin/users/{id?}/auth', 'UsersController@auth')->name('auth');
    // Удаленные события
    Route::get('/events/trashed', 'EventsController@indexTrashed')->name('events-trashed');
    Route::post('/events/{id?}/restore', 'EventsController@restore')->name('event-restore');
    Route::post('/events/{id?}/destroy', 'EventsController@forceDelete')->name('event-destroy');
});

// Маршруты, доступные авторизованным пользователям
Route::middleware(['auth'])->group(function () {
    // Выход из приложения
    Route::get('/logout', 'Auth\LoginController@logout')->name('logout-get');
    // События
    Route::get('/events/filters', 'EventsController@filters')->name('event-filters');
    Route::post('/events/filters', 'EventsController@setFilters');
    Route::get('/events/search', 'EventsController@search')->name('search-event');
    Route::post('/events/search', 'EventsController@find')->name('find-event');
    Route::post('/events/get-categories', 'EventsController@getEventCategories')->name('get-event-categories');
    Route::post('/events/get-flights', 'EventsController@getFlights')->name('get-flights');
    Route::get('/events/create', 'EventsController@create')->name('create-event');
    Route::post('/events/create', 'EventsController@store');
    Route::get('/events/{id?}', 'EventsController@show')->name('view-event');
    Route::get('/events/{id?}/edit', 'EventsController@edit')->name('edit-event');
    Route::post('/events/{id?}/edit', 'EventsController@update');
    Route::get('/', 'EventsController@index')->name('home');
});
