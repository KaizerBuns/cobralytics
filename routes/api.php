<?php

use App\User;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:api')->get('/user', function (Request $request) {
	return $request->user();
});

Route::get('/create/user', function (Request $request) {

	$validator = Validator::make($request->all(), [
		'name' => 'required|string|max:255',
		'email' => 'required|string|email|max:255|unique:users',
		'password' => 'required|string|min:6|confirmed',
		'terms' => 'required',
	]);

	// then, if it fails, return the error messages in JSON format
	if ($validator->fails()) {
		return response()->json($validator->messages(), 200);
	}

	$user = User::create([
		'name' => $request->input('name'),
		'email' => $request->input('email'),
		'password' => Hash::make($request->input('password')),
		'enable_campaigns' => 0,
		'enable_offers' => 0,
		'enable_monitors' => 0,
		'enable_reports' => 0,
		'enable_analytics' => 0,
	]);

	if (is_null($user)) {
		Log::error('User registration Failed');
	}

	$project = Project::create([
		'user_id' => $user->id,
		'name' => 'My First Project',
		'description' => 'Project created when you first registered.',
		'is_default' => 1,
	]);

	if (is_null($project)) {
		Log::error('Default Project on user registration Failed');
	}

	return response()->json(['success' => 'ok'], 200);
});