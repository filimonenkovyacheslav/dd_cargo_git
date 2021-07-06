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


/*
*  Front
*/
Route::group(['prefix' => App\Http\Middleware\LocaleMiddleware::getLocale()], function(){
	
	Route::get('/', function () {
		return view('welcome');
	})->name('welcome');

	Route::get('/page-{page_urn}','Admin\FrontPagesController@frontPage')->name('frontPage');
	
	Route::get('/parcel-form', 'FrontController@parcelForm')->name('parcelForm');

	Route::post('/parcel-form', 'FrontController@newParcelAdd')->name('newParcelAdd');	

	Route::post('/check-phone',['uses' => 'FrontController@checkPhone','as' => 'checkPhone']);

	Route::post('/phil-ind-check-phone',['uses' => 'FrontController@philIndCheckPhone','as' => 'philIndCheckPhone']);

	Route::get('/tracking-form', 'FrontController@trackingForm')->name('trackingForm');

	Route::post('/tracking-form', 'FrontController@getTracking')->name('getTracking');

	Route::get('/china-parcel-form', 'FrontController@chinaParcelForm')->name('chinaParcelForm');

	Route::post('/china-parcel-form', 'FrontController@chinaParcelAdd')->name('chinaParcelAdd');

	Route::get('/phil-ind-parcel-form', 'FrontController@philIndParcelForm')->name('philIndParcelForm');

	Route::post('/phil-ind-parcel-form', 'FrontController@philIndParcelAdd')->name('philIndParcelAdd');
});


// Альтернатива php artisan storage:link
Route::get('storage/{filename}', function ($filename)
{
    $path = storage_path('public/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});


//Переключение языков
Route::get('setlocale/{lang}', function ($lang) {

    $referer = Redirect::back()->getTargetUrl(); //URL предыдущей страницы
    $parse_url = parse_url($referer, PHP_URL_PATH); //URI предыдущей страницы

    //разбиваем на массив по разделителю
    $segments = explode('/', $parse_url);

    //Если URL (где нажали на переключение языка) содержал корректную метку языка
    if (in_array($segments[1], App\Http\Middleware\LocaleMiddleware::$languages)) {

        unset($segments[1]); //удаляем метку
    } 
    
    //Добавляем метку языка в URL (если выбран не язык по-умолчанию)
    if ($lang != App\Http\Middleware\LocaleMiddleware::$mainLanguage){ 
        array_splice($segments, 1, 0, $lang); 
    }	

    //формируем полный URL
    $url = Request::root().implode("/", $segments);
    
    //если были еще GET-параметры - добавляем их
    if(parse_url($referer, PHP_URL_QUERY)){    
        $url = $url.'?'. parse_url($referer, PHP_URL_QUERY);
    }

    return redirect($url); //Перенаправляем назад на ту же страницу                            

})->name('setlocale');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


/*
*  Admin
*/
Route::group(['prefix' => 'admin','middleware' => 'auth'],function() {	
	
	// Partners
	Route::get('/partners',['uses' => 'Admin\PartnersController@index','as' => 'adminPartners']);
	
	Route::get('/partners-up/{role}', 'Admin\PartnersController@show');

	Route::post('/partners-up/{role}',['uses'=>'Admin\PartnersController@update','as'=>'partnerUpdate']);	
	
	// Users
	Route::get('/',['uses' => 'Admin\IndexController@index','as' => 'adminIndex']);

	Route::get('/users',['uses' => 'Admin\RolesController@index','as' => 'adminUsers']);

	Route::get('/users/{id}', 'Admin\RolesController@show');

	Route::post('/users/{id}',['uses'=>'Admin\RolesController@update','as'=>'userUpdate']);

	Route::post('/users',['uses' => 'Admin\RolesController@destroy','as' => 'deleteUser']);

	Route::get('/user-add',['uses'=>'Admin\RolesController@showAdd','as'=>'showUser']);

	Route::post('/user-add',['uses'=>'Admin\RolesController@add','as'=>'userAdd']);
	
	// Worksheet
	Route::get('/worksheet',['uses' => 'Admin\WorksheetController@index','as' => 'adminWorksheet']);

	Route::get('/worksheet/batch-number',['uses' => 'Admin\WorksheetController@showStatus','as' => 'showStatus']);

	Route::post('/worksheet/batch-number',['uses' => 'Admin\WorksheetController@changeStatus','as' => 'changeStatus']);

	Route::get('/worksheet/date',['uses' => 'Admin\WorksheetController@showStatusDate','as' => 'showStatusDate']);

	Route::post('/worksheet/date',['uses' => 'Admin\WorksheetController@changeStatusDate','as' => 'changeStatusDate']);

	Route::get('/worksheet-update/{id}', ['uses' => 'Admin\WorksheetController@show','as' => 'adminWorksheetShow']);

	Route::post('/worksheet-update/{id}',['uses'=>'Admin\WorksheetController@update','as'=>'worksheetUpdate']);

	Route::post('/delete-worksheet',['uses' => 'Admin\WorksheetController@destroy','as' => 'deleteWorksheet']);

	// Draft worksheet
	Route::get('/draft-worksheet',['uses' => 'Admin\DraftWorksheetController@index','as' => 'adminDraftWorksheet']);

	Route::get('/draft-worksheet/{id}', ['uses' => 'Admin\DraftWorksheetController@show','as' => 'adminDraftWorksheetShow']);

	Route::post('/draft-worksheet/{id}',['uses'=>'Admin\DraftWorksheetController@update','as'=>'draftWorksheetUpdate']);

	Route::post('/draft-worksheet',['uses' => 'Admin\DraftWorksheetController@destroy','as' => 'deleteDraftWorksheet']);

	Route::get('/draft-worksheet-add',['uses'=>'Admin\DraftWorksheetController@showAdd','as'=>'showDraftWorksheet']);

	Route::post('/draft-worksheet-add',['uses'=>'Admin\DraftWorksheetController@add','as'=>'draftWorksheetAdd']);

	Route::post('/draft-worksheet-id-data',['uses' => 'Admin\DraftWorksheetController@addDraftDataById','as' => 'addDraftDataById']);

	Route::post('/draft-worksheet-id-data-delete',['uses' => 'Admin\DraftWorksheetController@deleteDraftWorksheetById','as' => 'deleteDraftWorksheetById']);

	Route::get('/draft-worksheet-filter',['uses' => 'Admin\DraftWorksheetController@draftWorksheetFilter','as' => 'draftWorksheetFilter']);

	Route::get('/draft-activate/{id}', ['uses' => 'Admin\DraftWorksheetController@draftActivate','as' => 'draftActivate']);

	// New worksheet
	Route::get('/new-worksheet',['uses' => 'Admin\NewWorksheetController@index','as' => 'adminNewWorksheet']);

	Route::get('/new-worksheet/{id}', ['uses' => 'Admin\NewWorksheetController@show','as' => 'adminNewWorksheetShow']);

	Route::post('/new-worksheet/{id}',['uses'=>'Admin\NewWorksheetController@update','as'=>'newWorksheetUpdate']);

	Route::post('/new-worksheet',['uses' => 'Admin\NewWorksheetController@destroy','as' => 'deleteNewWorksheet']);

	Route::get('/new-worksheet-add',['uses'=>'Admin\NewWorksheetController@showAdd','as'=>'showNewWorksheet']);

	Route::post('/new-worksheet-add',['uses'=>'Admin\NewWorksheetController@add','as'=>'newWorksheetAdd']);

	Route::get('/new-worksheet-add-column',['uses'=>'Admin\NewWorksheetController@addColumn','as'=>'newWorksheetAddColumn']);
	Route::post('/new-worksheet-add-column',['uses'=>'Admin\NewWorksheetController@deleteColumn','as'=>'newWorksheetDeleteColumn']);

	Route::get('/new-worksheet-batch-number',['uses' => 'Admin\NewWorksheetController@showNewStatus','as' => 'showNewStatus']);

	Route::post('/new-worksheet-batch-number',['uses' => 'Admin\NewWorksheetController@changeNewStatus','as' => 'changeNewStatus']);

	Route::get('/new-worksheet-date',['uses' => 'Admin\NewWorksheetController@showNewStatusDate','as' => 'showNewStatusDate']);

	Route::post('/new-worksheet-date',['uses' => 'Admin\NewWorksheetController@changeNewStatusDate','as' => 'changeNewStatusDate']);

	Route::get('/new-worksheet-tracking-data',['uses' => 'Admin\NewWorksheetController@showNewData','as' => 'showNewData']);

	Route::post('/new-worksheet-tracking-data',['uses' => 'Admin\NewWorksheetController@addNewData','as' => 'addNewData']);	

	Route::post('/new-worksheet-id-data',['uses' => 'Admin\NewWorksheetController@addNewDataById','as' => 'addNewDataById']);

	Route::post('/new-worksheet-id-data-delete',['uses' => 'Admin\NewWorksheetController@deleteNewWorksheetById','as' => 'deleteNewWorksheetById']);

	Route::get('/new-worksheet-filter',['uses' => 'Admin\NewWorksheetController@newWorksheetFilter','as' => 'newWorksheetFilter']);

	// Packing Sea
	Route::get('/packing-sea',['uses' => 'Admin\NewWorksheetController@indexPackingSea','as' => 'indexPackingSea']);

	Route::get('/packing-sea/{id}', ['uses' => 'Admin\NewWorksheetController@showPackingSea','as' => 'showPackingSea']);

	Route::post('/packing-sea/{id}',['uses'=>'Admin\NewWorksheetController@updatePackingSea','as'=>'updatePackingSea']);

	Route::post('/packing-sea',['uses' => 'Admin\NewWorksheetController@destroyPackingSea','as' => 'destroyPackingSea']);

	Route::get('/packing-sea-add',['uses'=>'Admin\NewWorksheetController@showAddPackingSea','as'=>'showAddPackingSea']);

	Route::post('/packing-sea-add',['uses'=>'Admin\NewWorksheetController@addPackingSea','as'=>'addPackingSea']);

	// Export to Excel
	Route::get('/new-worksheet-export',['uses' => 'Admin\NewWorksheetController@exportExcel','as' => 'exportExcelNew']);

	Route::get('/worksheet-export',['uses' => 'Admin\WorksheetController@exportExcel','as' => 'exportExcel']);

	Route::get('/packing-sea-export',['uses' => 'Admin\NewWorksheetController@exportExcelPackingSea','as' => 'exportExcelPackingSea']);

	// Front pages
	Route::get('/front-pages',['uses' => 'Admin\FrontPagesController@index','as' => 'frontPages']);

	Route::get('/add-front-page',['uses' => 'Admin\FrontPagesController@addFrontPage','as' => 'addFrontPage']);

	Route::post('/add-front-page',['uses' => 'Admin\FrontPagesController@createFrontPage','as' => 'createFrontPage']);

	Route::get('/update-front-page/{id}',['uses' => 'Admin\FrontPagesController@adminFrontPage','as' => 'adminFrontPage']);

	Route::post('/update-front-page/{id}',['uses' => 'Admin\FrontPagesController@updateFrontPage','as' => 'updateFrontPage']);

	Route::post('/delete-front-page',['uses' => 'Admin\FrontPagesController@deleteFrontPage','as' => 'deleteFrontPage']);
    
    Route::post('ckeditor/image_upload', 'CKEditorController@upload')->name('upload');
});
/*
*  End Admin
*/


/*
*  China admin
*/
Route::get('/admin/china',['uses' => 'Admin\IndexController@chinaIndex','as' => 'adminChinaIndex'])->middleware('can:china_rights');

// China users
Route::get('/admin/china-users',['uses' => 'Admin\ChinaRolesController@index','as' => 'adminChinaUsers'])->middleware('can:china_rights');

Route::get('/admin/china-users/{id}', 'Admin\ChinaRolesController@show')->middleware('can:china_rights');

Route::post('/admin/china-users/{id}',['uses'=>'Admin\ChinaRolesController@update','as'=>'userChinaUpdate'])->middleware('can:china_rights');

Route::post('/admin/china-users',['uses' => 'Admin\ChinaRolesController@destroy','as' => 'deleteChinaUser'])->middleware('can:china_rights');

Route::get('/admin/china-user-add',['uses'=>'Admin\ChinaRolesController@showAdd','as'=>'showChinaUser'])->middleware('can:china_rights');

Route::post('/admin/china-user-add',['uses'=>'Admin\ChinaRolesController@add','as'=>'userChinaAdd'])->middleware('can:china_rights');

// China worksheet
Route::get('/admin/china-worksheet', ['uses' => 'Admin\ChinaWorksheetController@index','as' => 'adminChinaWorksheet'])->middleware('can:china_rights');

Route::get('/admin/china-worksheet/{id}', ['uses' => 'Admin\ChinaWorksheetController@show','as' => 'adminChinaWorksheetShow'])->middleware('can:china_rights');

Route::post('/admin/china-worksheet/{id}',['uses'=>'Admin\ChinaWorksheetController@update','as'=>'chinaWorksheetUpdate'])->middleware('can:china_rights');

Route::post('/admin/china-worksheet',['uses' => 'Admin\ChinaWorksheetController@destroy','as' => 'deleteChinaWorksheet'])->middleware('can:china_rights');

Route::get('/admin/china-worksheet-add',['uses'=>'Admin\ChinaWorksheetController@showAdd','as'=>'showChinaWorksheet'])->middleware('can:china_rights');

Route::post('/admin/china-worksheet-add',['uses'=>'Admin\ChinaWorksheetController@add','as'=>'chinaWorksheetAdd'])->middleware('can:china_rights');

Route::get('/admin/china-worksheet-add-column',['uses'=>'Admin\ChinaWorksheetController@addColumn','as'=>'chinaWorksheetAddColumn'])->middleware('can:china_rights');
Route::post('/admin/china-worksheet-add-column',['uses'=>'Admin\ChinaWorksheetController@deleteColumn','as'=>'chinaWorksheetDeleteColumn'])->middleware('can:china_rights');

Route::get('/admin/china-worksheet-batch-number',['uses' => 'Admin\ChinaWorksheetController@showChinaStatus','as' => 'showChinaStatus'])->middleware('can:china_rights');

Route::post('/admin/china-worksheet-batch-number',['uses' => 'Admin\ChinaWorksheetController@changeChinaStatus','as' => 'changeChinaStatus'])->middleware('can:china_rights');

Route::get('/admin/china-worksheet-date',['uses' => 'Admin\ChinaWorksheetController@showChinaStatusDate','as' => 'showChinaStatusDate'])->middleware('can:china_rights');

Route::post('/admin/china-worksheet-date',['uses' => 'Admin\ChinaWorksheetController@changeChinaStatusDate','as' => 'changeChinaStatusDate'])->middleware('can:china_rights');

// Export to Excel
Route::get('/admin/admin/china-worksheet-export',['uses' => 'Admin\ChinaWorksheetController@exportExcel','as' => 'exportExcelChina'])->middleware('can:china_rights');
/*
*  End China admin
*/


/*
*  Philippines India admin
*/
Route::get('/admin/phil-ind',['uses' => 'Admin\IndexController@philIndIndex','as' => 'adminPhilIndIndex'])->middleware('can:phil_ind_rights');

// Philippines India users
Route::get('/admin/phil-ind-users',['uses' => 'Admin\PhilIndRolesController@index','as' => 'adminPhilIndUsers'])->middleware('can:phil_ind_rights');

Route::get('/admin/phil-ind-users/{id}', 'Admin\PhilIndRolesController@show')->middleware('can:phil_ind_rights');

Route::post('/admin/phil-ind-users/{id}',['uses'=>'Admin\PhilIndRolesController@update','as'=>'userPhilIndUpdate'])->middleware('can:phil_ind_rights');

Route::post('/admin/phil-ind-users',['uses' => 'Admin\PhilIndRolesController@destroy','as' => 'deletePhilIndUser'])->middleware('can:phil_ind_rights');

Route::get('/admin/phil-ind-user-add',['uses'=>'Admin\PhilIndRolesController@showAdd','as'=>'showPhilIndUser'])->middleware('can:phil_ind_rights');

Route::post('/admin/phil-ind-user-add',['uses'=>'Admin\PhilIndRolesController@add','as'=>'userPhilIndAdd'])->middleware('can:phil_ind_rights');

// Philippines India Draft
Route::get('/admin/eng-draft-worksheet', ['uses' => 'Admin\EngDraftWorksheetController@index','as' => 'adminEngDraftWorksheet'])->middleware('can:phil_ind_rights');

Route::get('/admin/eng-draft-worksheet/{id}', ['uses' => 'Admin\EngDraftWorksheetController@show','as' => 'adminEngDraftWorksheetShow'])->middleware('can:phil_ind_rights');

Route::post('/admin/eng-draft-worksheet/{id}',['uses'=>'Admin\EngDraftWorksheetController@update','as'=>'engDraftWorksheetUpdate'])->middleware('can:phil_ind_rights');

Route::post('/admin/eng-draft-worksheet',['uses' => 'Admin\EngDraftWorksheetController@destroy','as' => 'deleteEngDraftWorksheet'])->middleware('can:phil_ind_rights');

Route::get('/admin/eng-draft-worksheet-add',['uses'=>'Admin\EngDraftWorksheetController@showAdd','as'=>'showEngDraftWorksheet'])->middleware('can:phil_ind_rights');

Route::post('/admin/eng-draft-worksheet-add',['uses'=>'Admin\EngDraftWorksheetController@add','as'=>'engDraftWorksheetAdd'])->middleware('can:phil_ind_rights');

Route::get('/eng-draft-worksheet-filter',['uses' => 'Admin\EngDraftWorksheetController@engDraftWorksheetFilter','as' => 'engDraftWorksheetFilter']);

Route::post('/eng-draft-worksheet-id-data',['uses' => 'Admin\EngDraftWorksheetController@addEngDraftDataById','as' => 'addEngDraftDataById']);

Route::post('/eng-draft-worksheet-id-data-delete',['uses' => 'Admin\EngDraftWorksheetController@deleteEngDraftWorksheetById','as' => 'deleteEngDraftWorksheetById']);

Route::get('/admin/eng-draft-activate/{id}', ['uses' => 'Admin\EngDraftWorksheetController@engDraftActivate','as' => 'engDraftActivate'])->middleware('can:phil_ind_rights');

// Philippines India Worksheet
Route::get('/admin/phil-ind-worksheet', ['uses' => 'Admin\PhilIndWorksheetController@index','as' => 'adminPhilIndWorksheet'])->middleware('can:phil_ind_rights');

Route::get('/admin/phil-ind-worksheet/{id}', ['uses' => 'Admin\PhilIndWorksheetController@show','as' => 'adminPhilIndWorksheetShow'])->middleware('can:phil_ind_rights');

Route::post('/admin/phil-ind-worksheet/{id}',['uses'=>'Admin\PhilIndWorksheetController@update','as'=>'philIndWorksheetUpdate'])->middleware('can:phil_ind_rights');

Route::post('/admin/phil-ind-worksheet',['uses' => 'Admin\PhilIndWorksheetController@destroy','as' => 'deletePhilIndWorksheet'])->middleware('can:phil_ind_rights');

Route::get('/admin/phil-ind-worksheet-add',['uses'=>'Admin\PhilIndWorksheetController@showAdd','as'=>'showPhilIndWorksheet'])->middleware('can:phil_ind_rights');

Route::post('/admin/phil-ind-worksheet-add',['uses'=>'Admin\PhilIndWorksheetController@add','as'=>'philIndWorksheetAdd'])->middleware('can:phil_ind_rights');

Route::get('/admin/phil-ind-worksheet-add-column',['uses'=>'Admin\PhilIndWorksheetController@addColumn','as'=>'philIndWorksheetAddColumn'])->middleware('can:phil_ind_rights');
Route::post('/admin/phil-ind-worksheet-add-column',['uses'=>'Admin\PhilIndWorksheetController@deleteColumn','as'=>'philIndWorksheetDeleteColumn'])->middleware('can:phil_ind_rights');

Route::get('/admin/phil-ind-worksheet-batch-number',['uses' => 'Admin\PhilIndWorksheetController@showPhilIndStatus','as' => 'showPhilIndStatus'])->middleware('can:phil_ind_rights');

Route::post('/admin/phil-ind-worksheet-batch-number',['uses' => 'Admin\PhilIndWorksheetController@changePhilIndStatus','as' => 'changePhilIndStatus'])->middleware('can:phil_ind_rights');

Route::get('/admin/phil-ind-worksheet-date',['uses' => 'Admin\PhilIndWorksheetController@showPhilIndStatusDate','as' => 'showPhilIndStatusDate'])->middleware('can:phil_ind_rights');

Route::post('/admin/phil-ind-worksheet-date',['uses' => 'Admin\PhilIndWorksheetController@changePhilIndStatusDate','as' => 'changePhilIndStatusDate'])->middleware('can:phil_ind_rights');

Route::get('/phil-ind-worksheet-tracking-data',['uses' => 'Admin\PhilIndWorksheetController@showPhilIndData','as' => 'showPhilIndData']);

Route::post('/phil-ind-worksheet-tracking-data',['uses' => 'Admin\PhilIndWorksheetController@addPhilIndData','as' => 'addPhilIndData']);

Route::get('/phil-ind-worksheet-filter',['uses' => 'Admin\PhilIndWorksheetController@philIndWorksheetFilter','as' => 'philIndWorksheetFilter']);

Route::post('/phil-ind-worksheet-id-data',['uses' => 'Admin\PhilIndWorksheetController@addPhilIndDataById','as' => 'addPhilIndDataById']);

Route::post('/phil-ind-worksheet-id-data-delete',['uses' => 'Admin\PhilIndWorksheetController@deletePhilIndWorksheetById','as' => 'deletePhilIndWorksheetById']);

// Packing Eng
Route::get('/admin/packing-eng',['uses' => 'Admin\PhilIndWorksheetController@indexPackingEng','as' => 'indexPackingEng']);

Route::get('/admin/packing-eng/{id}', ['uses' => 'Admin\PhilIndWorksheetController@showPackingEng','as' => 'showPackingEng']);

Route::post('/admin/packing-eng/{id}',['uses'=>'Admin\PhilIndWorksheetController@updatePackingEng','as'=>'updatePackingEng']);

Route::post('/admin/packing-eng',['uses' => 'Admin\PhilIndWorksheetController@destroyPackingEng','as' => 'destroyPackingEng']);

Route::get('/admin/packing-eng-add',['uses'=>'Admin\PhilIndWorksheetController@showAddPackingEng','as'=>'showAddPackingEng']);

Route::post('/admin/packing-eng-add',['uses'=>'Admin\PhilIndWorksheetController@addPackingEng','as'=>'addPackingEng']);

// Export to Excel
Route::get('/admin/admin/phil-ind-worksheet-export',['uses' => 'Admin\PhilIndWorksheetController@exportExcel','as' => 'exportExcelPhilInd'])->middleware('can:phil_ind_rights');

Route::get('/admin/admin/packing-eng-export',['uses' => 'Admin\PhilIndWorksheetController@exportExcelPackingEng','as' => 'exportExcelPackingEng']);
/*
*  End Philippines India admin
*/