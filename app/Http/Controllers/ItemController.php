<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\ItemRepository;
use App\Repositories\LocationRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use App\Repositories\ViewRepository;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Notifications\NewComment;


class ItemController extends Controller
{
    private $itemRepository, $locationRepository, $tagRepository, $userRepository, $viewRepository;

    public function __construct(ItemRepository $itemRepo,
                                LocationRepository $locationRepo,
                                TagRepository $tagRepo,
                                UserRepository $userRepo,
                                ViewRepository $viewRepo) {
        $this->itemRepository       = $itemRepo;
        $this->locationRepository   = $locationRepo;
        $this->tagRepository        = $tagRepo;
        $this->viewRepository       = $viewRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /*$token     = JWTAuth::getToken();
        $user      = JWTAuth::toUser($token);*/
        //$itemQuery = $user->items();

        if(!is_null($itemId = $request->get('item'))) {
            //returns items that are comments
            return response()->json($this->itemRepository->findAllBy('item_id', $itemId));
        }

        //returns plain items
        return response()->json($this->itemRepository->findAllBy('item_id', NULL));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user = JWTAuth::toUser(JWTAuth::getToken());
           
        $data    = $request->input();
        $title   = $data['title'];
        $content = $data['content'];

        $data['title']   = utf8_encode($data['title']);
        $data['content'] = utf8_encode($data['content']);
        $data['user_id'] = $user->id;

        $new_item = $this->itemRepository->create($data);
        $location  = $this->locationRepository->create($data);

        //if item is a comment, send notifs, don't save tags
        if(!empty($data['item_id'])) {
            $item  = $this->itemRepository->find($data['item_id']);
            $owner = $this->userRepository->find($item->user_id);
            //check if comment is not from item owner
            if($owner->id != $user->id) {
                $owner->notifyClient(new NewComment($item, $user));
            }
        } 
        else {
            $this->tagRepository->createFromArray($new_item, [$title, $content]);
        }

        $new_item->locations()->save($location);

        return response()->json($new_item);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = auth()->user();
        $item = $this->itemRepository->find($id);
        $item->comments = $item->comments;

        if($item->user_id != $user->id) {
            $this->viewRepository->firstOrCreate($user, $item);
        }

        $viewCount = $this->viewRepository->getItemViewCount($item);

        //notification here

        return response()->json($item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
