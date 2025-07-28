<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Events;
use App\Repositories\BaseRepository;

class EventRepository extends BaseRepository {
    

    public function model():string
    {
        return Events::class;
    }

    public function getFieldsSearchable(): array
    {
        return [
            'title',
            'description',
            'location',
            'start_at',
            'end_at'
        ];
    }

    public function getEvents(){
        return $this->model->get();
    }

    public function storeEvents($input):Events
    {
        $event =  $this->model->create(
            [
                'title' => $input->title,
                'description' => $input->description,
                'location' => $input->location,
                'start_at' => $input->start_at,
                'end_at' => $input->end_at,
                'author_id' => auth()->id()
            ]
            );

        return $event;
    }

    public function allUserEvents()
    {
        $userEvents = User::with('events')>select('id', 'name')->has('events')->get();
        return $userEvents;
    }

    public function showUserEvent($id)
    {   
        $user = User::with('events')->select('id', 'name')->where('id', $id)->first();
        return $user;
    }

    public function getFilter($filters){
       return $this->model->filter($filters)->get();
    }



}