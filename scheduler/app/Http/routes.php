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
use App\Models\User;
use App\Models\Item;
use App\Models\Signup;
use App\Geolocation;
use App\Services\EmojiParser;
use App\Repositories\TagRepository;

Route::get('/', function (Request $request, TagRepository $tagRepo) {
   /*$response = \GoogleMaps::load('geocoding')
        ->setParam (['address' =>'santa cruz'])
        ->get();
    return response()->json($response);*/
    
    $matches = [];
    $items = Item::all();
    $stringToParse = $items[9]->title;
    //$tagRepo->createFromArray($items[2], [$items[2]->content, $items[3]->content]);

    echo var_dump(preg_match_all('/\:(.*?)\:/', LaravelEmojiOne::toShort($stringToParse), $matches));
    
    $emoji_array = EmojiParser::parse($stringToParse); //array_unique($matches[1]);
    echo var_dump($emoji_array);

    foreach ($emoji_array as $emoji) {
      echo $emoji." - ".LaravelEmojiOne::shortnameToUnicode(":".$emoji.":")."<br/>";
    }
    /*$items = Item::all();
    return response()->json($items);*/
    //return view('welcome');
});

//deprecated
Route::get('sign-up-facebook', function (Request $request) {
    $signup_session = $request->signup;
    $request->session()->flash('signup', $signup_session);
    return Socialite::driver('facebook')->redirect();
});

//deprecated
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

//deprecated
Route::post('fb-sign-up-from-app', ['middleware' => 'cors', function (Request $request) {
    $signup = new Signup;
    
    $signup->session = $request->session()->token();
    $signup->username = $request->username;
    $signup->password = Hash::make($request->password);
    
    $signup->save();
    
    return response()->json($request->session()->token());
}]);

Route::post('fb-sign-up', ['middleware' => 'cors', function (Request $request) {
    $data = $request->all();

    if(!empty($data['facebook'])) {
      $facebook_user = Socialite::driver('facebook')->userFromToken($data['access']);

      if(!empty($facebook_user)) {
          $user = new User;
          $user->name = $data['username'];
          $user->password = bcrypt(str_random(12));
          $user->facebook = $data['facebook'];
          $user->email = $facebook_user->getEmail();
          $user->save();

          return response()->json($user);
      }
    }
   // return response()->json($data);
}]);

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

Route::get('test-token', ['middleware' => 'cors', function() {
    return response()->json(['token' => Hash::make(Config::get('app.mobile_app_token'))]);
}]);

Route::get('test-user-auth', function () {
    if (Auth::attempt(['email' => 'faith_xyz@yahoo.com', 'password' => 'stark'])) {
        // Authentication passed...
       echo 'valid';
    }
    else echo 'not valid';
});

Route::post('password-log-in', ['middleware' => ['cors'], function(Request $request) {

    if ( ! $token = JWTAuth::attempt(['name' => $request->username,
                                      'password' => $request->password])) {
        return Response::json(false, HttpResponse::HTTP_UNAUTHORIZED);
    }
    
    return Response::json(compact('token'));
    //return Response::json($request->input());
}]);

Route::post('facebook-log-in', ['middleware' => ['cors'], function(Request $request) {
    //TODO: check access token for better security
    $user = User::where('facebook', '=', $request->facebook)->first();

    if ( ! $token = JWTAuth::fromUser($user)) {
        return Response::json(false, HttpResponse::HTTP_UNAUTHORIZED);
    }
    
    return Response::json(compact('token'));
}]);

Route::group(['middleware' => 'jwt.auth'], function () {
  Route::get('get-items', [
        'as'   => 'getItems',
        'uses' => 'ItemController@index',
      ]);
  Route::post('save-item', [
        'as'   => 'saveItem',
        'uses' => 'ItemController@store',
      ]);
  Route::get('item/{id}', [
        'as'   => 'showItem',
        'uses' => 'ItemController@show',
      ]);
});
