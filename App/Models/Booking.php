<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'event_id','number_of_seats'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Fonction pour récupérer les réservations avec les utilisateurs et les événements
    public static function getBookingsWithUsersAndEvents()
    {
        return self::with('user', 'event')->get();
    }

    // Fonction pour formater les réservations en JSON
    public static function getBookingsAsJson()
    {
        $bookings = self::getBookingsWithUsersAndEvents();
        $data = [];

        foreach ($bookings as $booking) {
            $data[] = [
                'id' => $booking->id,
                'user' => [
                    'id' => $booking->user->id,
                    'name' => $booking->user->name,
                    'lastname' => $booking->user->lastname,
                    'email' => $booking->user->email,
                ],
                'event' => [
                    'id' => $booking->event->id,
                    'title' => $booking->event->title,
                    'description' => $booking->event->description,
                    'event_date' => $booking->event->event_date,
                    'lieu' => $booking->event->lieu,
                    'image' =>Storage::disk('public')->url($booking->event->image), 
                ],
                'number_of_seats' => $booking->number_of_seats,
            ];
        }

        return response()->json($data);
    }
}
