<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use App\User;
use App\Item;
use App\Signup;
use App\Geolocation;

Route::get('/', function () {
   $response = \GoogleMaps::load('geocoding')
        ->setParam (['address' =>'santa cruz'])
        ->get();
    return response()->json($response);
});

Route::get('sign-up-facebook', function (Request $request) {
    $signup_session = $request->signup;
    $request->session()->flash('signup', $signup_session);
    return Socialite::driver('facebook')->redirect();
});

Route::get('facebook-callback', function (Request $request) {
    
    $save_signup = Signup::where('session', $request->session()->get('signup'))
                                 ->orderBy('created_at', 'desc')
                                 ->first();
    $facebook_user = Socialite::driver('facebook')->user();
    $user = new User;
    $user->email = $facebook_user->getEmail();
    $user->name = $save_signup->username;
    $user->password = $save_signup->password;
    
    $user->save();
    
    return view('welcome');
});

Route::get('test-save', function (Request $request) {
   
   $edison = GeoLocation::fromDegrees(7.448386, 125.809143);
   $coordinates = $edison->boundingCoordinates(1, 'miles');
   
   echo "min latitude: " . $coordinates[0]->getLatitudeInDegrees() . " \n";
   echo "min longitude: " . $coordinates[0]->getLongitudeInDegrees() . " \n";
   
   echo "max latitude: " . $coordinates[1]->getLatitudeInDegrees() . " \n";
   echo "max longitude: " . $coordinates[1]->getLongitudeInDegrees() . " \n";
    //return response()->json($transaction);
});

Route::post('test-save-from-app', ['middleware' => 'cors', function (Request $request) {
    $transaction = new Item;
    
    $transaction->content = $request->title;
    
    $transaction->save();
    
    return response()->json($request);
}]);

Route::post('fb-sign-up-from-app', ['middleware' => 'cors', function (Request $request) {
    $signup = new Signup;
    
    $signup->session = $request->session()->token();
    $signup->username = $request->username;
    $signup->password = Hash::make($request->password);
    
    $signup->save();
    
    return response()->json($request->session()->token());
}]);

Route::get('test-token', ['middleware' => 'cors', function() {
    return response()->json(Hash::make(Config::get('app.mobile_app_token')));
}]);

Route::get('test-user-auth', function () {
    if (Auth::attempt(['email' => 'faith_xyz@yahoo.com', 'password' => 'stark'])) {
        // Authentication passed...
       echo 'valid';
    }
    else echo 'not valid';
});

Route::post('test-auth', ['middleware' => ['cors'], function(Request $request) {

    if ( ! $token = JWTAuth::attempt(['name' => $request->username,
                                      'password' => $request->password])) {
        return Response::json(false, HttpResponse::HTTP_UNAUTHORIZED);
    }
    
    return Response::json(compact('token'));
    //return Response::json($request->input());
}]);

Route::get('test-angular2-jwt', ['middleware' => ['cors', 'jwt.auth'], function(Request $request) {
    return Response::json($request->input());
}]);

Route::post('save-item', ['middleware' => ['jwt.auth'], function(Request $request) {
   $token = JWTAuth::getToken();
   $user = JWTAuth::toUser($token);
       
   $data = $request->input();
   
   $new_item = new Item;
   $new_item->user_id = $user->id;
   $new_item->content = utf8_encode($request->content);
   $new_item->fill($data);
   $new_item->save();
   
   return response()->json($new_item);
   
}]);

Route::get('get-items', ['middleware' => ['jwt.auth'], function(Request $request) {
   $token = JWTAuth::getToken();
   $user = JWTAuth::toUser($token);
   $items = $user->items;
   return response()->json($items);
  
}]);