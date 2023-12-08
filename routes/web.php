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

Route::get('/', function () {
	return view('welcome');
});

Auth::routes();

Route::get('/', function () {
	if (in_array($_SERVER['HTTP_HOST'], config('app.app_ui'))) {
		//Login to APP Cobralytics
		return redirect('/login');
	} else {
		//RedirectEngine
		$redirect = new App\Helpers\RedirectEngine();
		$redirect->execute();
	}
});

//Fire Click
Route::get('/clck', function () {
	//RedirectEngine
	$redirect = new App\Helpers\RedirectEngine();
	$redirect->click();
});

//Fire Pixel
Route::get('/fpx', function () {
	$redirect = new App\Helpers\RedirectEngine();
	return $redirect->conversion();
});

//Fire Pixel
Route::get('/fpx.php', function () {
	$redirect = new App\Helpers\RedirectEngine();
	return $redirect->conversion();
});

Route::get('/rotator', function () {
	//RedirectEngine
	$redirect = new App\Helpers\RedirectEngine();
	$redirect->rotator();
});

Route::get('home', function () {
	return redirect('member/dashboard');
});

Route::group(['middleware' => 'auth', 'namespace' => 'Member', 'prefix' => 'member'], function () {
	Route::match(['get', 'post'], 'dashboard', 'DashboardController@index');
	Route::match(['get', 'post'], 'project', 'ProjectController@index');
	Route::match(['get', 'post'], 'report', 'ReportController@index');
	Route::match(['get', 'post'], 'campaign', 'CampaignController@index');
	Route::match(['get', 'post'], 'offer', 'OfferController@index');
	Route::match(['get', 'post'], 'pixel', 'PixelController@index');
	Route::match(['get', 'post'], 'domain', 'DomainController@index');
	Route::match(['get', 'post'], 'source', 'SourceController@index');
	Route::match(['get', 'post'], 'traffic', 'TrafficController@index');
	Route::match(['get', 'post'], 'advertiser', 'AdvertiserController@index');
	Route::match(['get', 'post'], 'vertical', 'VerticalController@index');
	Route::match(['get', 'post'], 'service', 'ServiceController@index');
	Route::match(['get', 'post'], 'admin', 'AdminController@index');
	Route::match(['get', 'post'], 'rule', 'RuleController@index');
	Route::match(['get', 'post'], 'dns', 'DNSController@index');
	Route::match(['get', 'post'], 'monitor', 'MonitorController@index');
	Route::match(['get', 'post'], 'account', 'AccountController@index');
	Route::match(['get', 'post'], 'creative', 'CreativeController@index');
	Route::match(['get', 'post'], 'dnswings', 'DNSWingsController@index');
	//needs work
	Route::match(['get', 'post'], 'analytics', 'AnalyticController@index');
});

Route::group(['prefix' => 'ajax', 'middleware' => 'auth'], function () {
	Route::get('search_regions', 'AjaxController@search_regions');
	Route::get('search_cities', 'AjaxController@search_cities');
	Route::get('search_rule_types', 'AjaxController@search_rule_types');
	Route::get('search_source', 'AjaxController@search_source');
	Route::get('search_pixel', 'AjaxController@search_pixel');
	Route::get('update_rule', 'AjaxController@update_rule');
	Route::get('switch_menu', 'AjaxController@switch_menu');
	Route::get('realtime', 'AjaxController@realtime');
	Route::get('get_image', 'AjaxController@get_image');
});

Route::get('/logout', 'Auth\LoginController@logout');
