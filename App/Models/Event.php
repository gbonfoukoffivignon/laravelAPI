<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;
protected $fillable =
[
    'title' ,
    'description',
    'event_date',
    'max_attendees',
    'available_seats',
    'lieu' ,
    'image',
];
    public function users()
    {
        return $this->belongsToMany(User::class, 'bookings')
        ->withPivot('number_of_seats')
        ->withTimestamps();
    }
}
