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

use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use App\Models\User;
use App\Models\Item;
use App\Models\Signup;
use App\Geolocation;
use App\Services\Emoji\EmojiParser;
use App\Repositories\TagRepository;
use LaravelFCM\Message\PayloadNotificationBuilder;
use App\Notifications\NewComment;

Route::get('/', function (Request $request, TagRepository $tagRepo) {
   /*$response = \GoogleMaps::load('geocoding')
        ->setParam (['address' =>'santa cruz'])
        ->get();
    return response()->json($response);*/
    
    $matches = [];
    $items = Item::all();
    $stringToParse = $items[0]->title;
    //$tagRepo->createFromArray($items[2], [$items[2]->content, $items[3]->content]);
    echo var_dump($stringToParse);
    echo var_dump(LaravelEmojiOne::toShort($stringToParse));
    
    $emoji_array = EmojiParser::parse($stringToParse); //array_unique($matches[1]);
    echo var_dump($emoji_array);

    foreach ($emoji_array as $emoji) {
      echo $emoji." - ".EmojiParser::shortnameToUnicode(":".$emoji.":")."<br/>";
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

    if (empty($request->facebook)) {
        return Response::json(false, HttpResponse::HTTP_UNAUTHORIZED);
    }

    $user = User::where('facebook', '=', $request->facebook)->first();

    if (is_null($user)) {
        return Response::json(false, HttpResponse::HTTP_UNAUTHORIZED);
    }
   
    if ( ! $token = JWTAuth::fromUser($user)) {
        return Response::json(false, HttpResponse::HTTP_UNAUTHORIZED);
    }
    
    if(!empty($deviceToken = $request->deviceToken)) {
      //TODO: more logic for device token here
      $user->device_token = $deviceToken;
      $user->save();
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
  Route::get('explore-feed', [
        'as'   => 'exploreFeed',
        'uses' => 'ExploreController@index',
      ]);
  Route::post('emoji', [
        'as'   => 'emojiFeed',
        'uses' => 'ExploreController@getItemsForEmoji',
      ]);
});

Route::get('test', function (Request $request) {
  /*$deviceToken = User::first()->device_token;
  
  $notificationBuilder = new PayloadNotificationBuilder();
  $notificationBuilder->setTitle('title')
                  ->setBody('it\'s still me!')
                  ->setSound('default')
                  ->setTag('test');

  $notification = $notificationBuilder->build();

  $result = FCM::sendTo($deviceToken, null, $notification);

  echo var_dump($result);*/

  $user  = User::find(2);
  $item  = Item::find(1);
  $actor = User::find(4);

  $user->notifyClient(new NewComment($item, $actor));
  $notifications = $user->unreadNotifications;

  /*$notification = $notifications[0];

  $notification->data = ['test' => 'This is a test'];

  $notification->save();*/

  echo var_dump($notifications->sortByDesc('updated_at')->all());

  //echo var_dump($notifications[0]->updated_)
});
