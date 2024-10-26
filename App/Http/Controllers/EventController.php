<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NewEventNotifications;
use Illuminate\Support\Facades\Notification;

class EventController extends Controller
{
    // Afficher tous les événements
    public function index()
    {
        $events = Event::all();
        if ($events->count() == 0) {
            return response()->json(["message"=>"Aucun évènements !!!!"]);
        }
        return response()->json(["évènements"=> EventResource::collection($events)], 200);
    }

    // Afficher un seul événement par son ID
    public function show($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['error' => 'Événement non trouvé'], 404);
        }

        return response()->json(["évènement"=> EventResource::make($event)], 200);
    }

    // Créer un nouvel événement
    public function store(EventRequest $request)
    {
        // Validation déjà effectuée avec EventRequest
        $validatedData = $request->validated();
    
        // Conversion de la date au format correct
        $validatedData['event_date'] = Carbon::parse($validatedData['event_date'])->toDateString();
    
        // Traiter l'image
        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('images', 'public'); // Stocker l'image dans le disque 'public'
        }
    
        // Créer un nouvel événement
        $event = Event::create($validatedData);
    
        // Obtenir l'URL de l'application (local ou distant)
        $baseUrl = config('app.url');
        /////////////////!!!!!!!!!!!
        // Exemple : Récupération de tous les utilisateurs à qui on veut envoyer la notification
        $users = User::where('role', 'user')->get(); // recuperer tous les utilisateur simple

        // Message de notification
        $message = +$event->title+" avec la description"+$event->description;
        $action='créé';
        // Envoi de la notification à la liste d'utilisateurs
        //Notification::send($users, new NewEventNotifications($message));
        foreach ($users as $user) {
            $user->notify(new NewEventNotifications($message, $action));
        }
        ////////////////!!!!!!!!!!!!
        // Retourner une réponse JSON pour indiquer le succès de l'enregistrement
        return response()->json([
            'success' => 'Événement enregistré avec succès','évènement' => [EventResource::make($event) ]], 201);
    }
    


    // Mettre à jour un événement existant
    public function update(EventRequest $request, $id)
    {
        dd($request->all());
        // Récupérer les données validées
        $validatedData = $request->validated();

        // Conversion de la date au bon format
        $validatedData['event_date'] = Carbon::parse($validatedData['event_date'])->toDateString();

        // Gestion de l'image si elle est présente
        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('images', 'public');
        }

        // Trouver l'événement à mettre à jour
        $event = Event::findOrFail($id);

        // Mise à jour de l'événement avec les données validées
        $event->update($validatedData);

        // Retourner une réponse de succès
        return response()->json([
            'success' => 'Événement mis à jour avec succès',
            'évènement' => $event
        ]);
    }

    // Supprimer un événement
    public function destroy($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(status: 404);
        }

        // Supprimer l'image si elle existe
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();
        $users = User::where('some_condition', true)->get(); // Obtenir les utilisateurs à notifier
        $message = "Cet événement "+$event->description;
        $action = "supprimé";
        foreach ($users as $user) {
            $user->notify(new NewEventNotifications($message,$action)); // Notifier chaque utilisateur
        }
        return response()->json(status: 200);
    }

    public function getEventByTitle($title)
{
    
    // Récupérer tous les événements en fonction du titre
    $events = Event::where('title', $title)
        ->select('id', 'title', 'description', 'event_date', 'max_attendees', 'available_seats', 'lieu', 'image')
        ->get(); // Utiliser get() pour obtenir tous les événements

    if ($events->count()>0) {
        
        
            return response()->json(['success'=> 'élément trouvé','events'=> EventResource::Collection($events)]);
        
        
    } else {
        return response()->json(['success' => 'Aucun événement trouvé avec ce titre','events'=> '0'], 404);
    }
}

}
