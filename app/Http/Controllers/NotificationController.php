<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{

    private function prepareForJson($notifications)
    {
        $notifications = $notifications->map(function ($notification, $key) {
            return [
                    'tag'        => $notification->id,
                    'item_id'    => $notification->data['item_id'],
                    'updated'    => $notification->updated_at->diffForHumans(),
                    'created_at' => $notification->created_at,
                    'read_at'    => $notification->read_at,
                    'text'       => $notification->data['text']
                ];
        });

        return $notifications->all();
    }

    public function newNotifications()
    {
        $results = [];

        $user = auth()->user();

        $notifications = $user->unreadNotifications->sortByDesc('updated_at');

        if(count($notifications) > 0) {
            $results = array_values($this->prepareForJson($notifications));
        }

        return response()->json($results);
    }

    public function allNotifications()
    {
        $results = [];

        $user = auth()->user();

        $notifications = $user->notifications->sortByDesc('updated_at');

        if(count($notifications) > 0) {
            $results = array_values($this->prepareForJson($notifications));
        }

        return response()->json($results);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markAsRead(Request $request)
    {
        $readNotifications = $request->get('ids');
        $user              = auth()->user();

        $notifications = $user->unreadNotifications->whereIn('id', $readNotifications);

        $notifications->markAsRead();

        return response()->json(count($notifications));
    }

}
