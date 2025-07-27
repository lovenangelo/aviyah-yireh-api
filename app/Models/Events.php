<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Events extends Model
{
    //
    protected $fillable = [
        'title',
        'description',
        'location',
        'start_at',
        'end_at',
        'author_id'
    ];

    public static array $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'location'=>'required|string',
        'start_at' => 'required|date',
        'end_at' => 'required|date',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, "author_id");
    }
      
}
