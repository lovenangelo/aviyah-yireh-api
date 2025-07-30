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
       return $this->model->create(
            [
                'title' => $input->title,
                'description' => $input->description,
                'location' => $input->location,
                'start_at' => $input->start_at,
                'end_at' => $input->end_at,
                'image_url'=> $input->image_url,
                'author_id' => auth()->id()
            ]
            );

 
    }

    public function allUserEvents()
    {
        return  User::with('events')->select('id', 'name')->has('events')->get();
        
    }

    public function showUserEvent($id)
    {
        return User::with('events')->select('id', 'name')->where('id', $id)->first();
      
    }

    public function getFilter($filters){
       return $this->model->filter($filters)->get();
    }

    public function bulkDestroy(array $eventIds)
    {
        $result = [
            "deleted"=> 0,
            "failed"=> 0,
            "failed_details"=>[]
        ];

        foreach($eventIds as $eventId){
            try{
                $this->delete($eventId);
                $result['deleted']++;
            }catch(\Exception $e){
                $result['failed']++;
                $result['failed_details'][]=[
                    'id'=> $eventId,
                    'reason'=> $e
                ];
            }
          
        }
        return $result;

    }


}