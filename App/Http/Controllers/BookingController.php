<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Requests\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Notifications\NewEventNotifications;
use Illuminate\Support\Facades\Notification;

class BookingController extends Controller
{
    //
    public function index(){
        return Booking::getBookingsAsJson(); 
    }
public function store(BookingRequest $request){

    $booking = Booking::create($request->all());
      $users = User::where('role', 'Admin')->get(); // recuperer tous les utilisateur simple

    // Message de notification
    $message = "Nouvel réservation de l'utilisateur N°: "+$booking->user_id+" avec le nombre de place reserver"+$booking->number_of_seats;
  
    // Envoi de la notification à la liste d'utilisateurs
    Notification::send($users, new NewEventNotifications($message,"créé"));

    ////////////////!!!!!!!!!!!!
    return response()->json(["success"=>"OK","reservation"=>BookingResource::make($booking)]);
}


public function destroy($id){
    $booking = Booking::find($id);
    $booking->delete();
       // Exemple : Récupération de tous les utilisateurs à qui on veut envoyer la notification
       $users = User::where('role', 'Admin')->get(); // recuperer tous les utilisateur simple

       // Message de notification
       $message = " réservation N°: "+$booking->id;
   
       // Envoi de la notification à la liste d'utilisateurs
       Notification::send($users, new NewEventNotifications($message," Annulée"));
   
       ////////////////!!!!!!!!!!!!
    return response()->json(['success'=> 'Réservation N°'+$id+' supprimer avec succès'],200);

}

// Récupère les réservations de l'utilisateur
public function getUserReservationsById(int $id, Request $request)
{
    // Récupérer l'utilisateur authentifié
    $authenticatedUser = $request->user(); // Utilise Sanctum pour obtenir l'utilisateur connecté

    // Vérifiez si l'utilisateur est authentifié
    if (!$authenticatedUser) {
        return response()->json([
            'status' => 'error',
            'message' => 'Utilisateur non authentifié.',
        ], 401);
    }

    // Vérifiez si l'utilisateur authentifié correspond à l'ID passé ou s'il a les droits pour accéder à ces informations
    if ($authenticatedUser->id !== $id) {
        return response()->json([
            'status' => 'error',
            'message' => 'Accès non autorisé à ces réservations.',
        ], 403);
    }

    // Récupérer les réservations de l'utilisateur par son ID
    $reservations = Booking::with(['user', 'event'])
        ->where('user_id', $id) // Utilisez l'ID fourni dans le paramètre
        ->get();

    // Vérifiez si l'utilisateur a des réservations
    if ($reservations->isEmpty()) {
        return response()->json([
            'status' => 'success',
            'message' => 'Aucune réservation trouvée pour cet utilisateur.',
            'data' => [],
        ], 200);
    }

    // Utilisation de la ressource pour formater les réservations
    return response()->json([
        'status' => 'success',
        'message' => 'Réservations récupérées avec succès.',
        'data' => $reservations, // Utilisation de la ressource BookingResource
    ], 200);
}

}
