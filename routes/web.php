<?php

Route::get('/manage/login', 'SessionsController@create')->name('manage-login');
Route::post('/manage/login', 'SessionsController@store');
Route::post('manage/logout', 'SessionsController@destroy');

Route::get('/manage', function () {
    $current='manage';
    $keys=[ 'CoursesAdmin','SignupsAdmin','UsersAdmin','MainSettings' ];
    $systems=[];

    foreach ($keys as $key) {
      
        $item=[
            'key' => $key,
            'menus' => \App\Services\Menus::adminMenus($current,$key)
        ];
        array_push($systems, $item);
        
    }
    
    return view('manage')->with([ 'systems' => $systems ]);

})->middleware('admin');;

//Auth::routes();

Route::group(['middleware' => 'admin'], function()
{
    Route::resource('/manage/change-password', 'ChangePasswordController',['only' => ['index','store']]);
    
    Route::resource('/manage/users', 'UsersController');
    Route::post('/manage/users/find', 'UsersController@find');

    Route::resource('/manage/terms', 'TermsController');

    Route::resource('/manage/admins', 'AdminsController');
    Route::post('/manage/admins/import', 'AdminsController@import');

    Route::resource('/manage/teachers', 'TeachersController');
    Route::post('/manage/teachers/import', 'TeachersController@import');
    Route::post('/manage/teachers/review', 'TeachersController@review');

    Route::resource('/manage/teacherGroups', 'TeacherGroupsController');
    Route::get('/manage/teacherGroups/{id}/teachers', 'TeacherGroupsController@teachers');
    Route::get('/manage/teacherGroups/{id}/EditTeacher', 'TeacherGroupsController@editTeacher');
    Route::put('/manage/teacherGroups/{id}/AddTeachers', 'TeacherGroupsController@addTeachers');
    Route::put('/manage/teacherGroups/{id}/RemoveTeacher', 'TeacherGroupsController@removeTeacher');


    Route::resource('/manage/categories', 'CategoriesController');
    Route::post('/manage/categories/importances', 'CategoriesController@importances');
    Route::post('/manage/categories/import', 'CategoriesController@import');

    Route::resource('/manage/centers', 'CentersController');
    Route::post('/manage/centers/importances', 'CentersController@importances');
    Route::post('/manage/centers/import', 'CentersController@import');

    Route::get('/manage/SeedDiscountCenters', 'CentersController@seedDiscountCenters');


    // Courses
    Route::resource('/manage/courses', 'CoursesController');
    Route::get('/manage/courses/{id}/EditInfo', 'CoursesController@editInfo');
    Route::put('/manage/courses/{id}/UpdateInfo', 'CoursesController@updateInfo');
    Route::post('/manage/courses/import', 'CoursesController@import');
    Route::post('/manage/courses/review', 'CoursesController@review');

    Route::resource('/manage/ClassTimes', 'ClassTimesController');
    Route::post('/manage/ClassTimes/import', 'ClassTimesController@import');

    Route::resource('/manage/processes', 'ProcessesController');
    Route::post('/manage/processes/import', 'ProcessesController@import');


    //Signups
    Route::resource('/manage/signups', 'SignupsController');

    
    Route::resource('/manage/contactInfoes', 'ContactInfoesController');

});
