<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\ItemRepository;
use App\Repositories\LocationRepository;
use App\Repositories\TagRepository;
use Tymon\JWTAuth\Facades\JWTAuth;

class ItemController extends Controller
{
    private $itemRepository, $locationRepository, $tagRepository;

    public function __construct(ItemRepository $itemRepo,
                                LocationRepository $locationRepo,
                                TagRepository $tagRepo) {
        $this->itemRepository       = $itemRepo;
        $this->locationRepository   = $locationRepo;
        $this->tagRepository        = $tagRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $token     = JWTAuth::getToken();
        $user      = JWTAuth::toUser($token);
        //$itemQuery = $user->items();

        if(!is_null($itemId = $request->get('item'))) {
            return response()->json($this->itemRepository->findAllBy('item_id', $itemId));
        }

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
       //$token = ;
       $user = JWTAuth::toUser(JWTAuth::getToken());
           
       $data    = $request->input();
       $title   = $data['title'];
       $content = $data['content'];

       $data['title']   = utf8_encode($data['title']);
       $data['content'] = utf8_encode($data['content']);
       $data['user_id'] = $user->id;
       
       $new_item = $this->itemRepository->create($data);
       $location  = $this->locationRepository->create($data);
       //No tags on comments
       $this->tagRepository->createFromArray($new_item, [$title, $content]);
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
        $item = $this->itemRepository->find($id);
        $item->comments = $item->comments;
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
