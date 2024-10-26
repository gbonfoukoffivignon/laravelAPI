<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\ServiceMessagerie;
use App\Http\Requests\UserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    //
    public function index(){
        $users = User::all();
        return response()->json(['users'=> UserResource::collection($users)],200);

    }
    
    public function store(UserRequest $request)
    {
     
    
        // Créer l'utilisateur
        $user = User::create($request->except('image')+['image'=>$request->photo->store('images', 'public')]);
    
        return response()->json(['success' => 'Utilisateur enregistré avec succès', 'user' => new UserResource($user)], 200);
    }
    
    public function show($id){
        $user = User::findOrFail($id);
        if(count($user)>0){
        return response()->json(['success'=> '1','user'=> new UserResource($user)],200);
        }
        return response()->json(['success'=> '0']);
    }
    public function update(UserRequest $request, $id){
        $user = User::find($id);
        $user->update($request->all());
        return response()->json(['success'=> 'Utilisateur Modifier avec succès','user'=> UserResource::make($user)],200);


    }
    public function destroy($id){
        $user = User::find($id);
        $user->delete();
        return response()->json(['success'=> 'Utilisateur N°'+$id+'avec succès'],200);

    }
    public function login(LoginRequest $request){

        //auth()->attempt(["username"=> $request->username,"password"=> $request->password]);
        $verif = auth()->attempt($request->all());
        
        if($verif){

           $accesToken = auth()->user()->createToken("vignon".str::random(5))->plainTextToken;
          
        return response()->json(['success'=>'1','user'=> UserResource::make(auth()->user()),'token'=>$accesToken]);
        } 
        return response()->json(['success'=> '0',],   422);
    }
    public function logout(LoginRequest $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json(['success' => 'déconnection avec succès !!!!!!']);
    }

    public function resetPassword(Request $request )
    {
        $request->validate(["email"=>"required|email"]);
        $user = User::where("email", $request->email)->firstOrFail();
        $motDePasse = Str::random(10);
        $user->update(["password"=> $motDePasse]);
        Mail::to($user->email)->send(new ServiceMessagerie($motDePasse));
    return response()->json(["success"=> "votre email a été réinitialisé, consulter votre email !!!!!!"],200);
     }

     public function getUserId(Request $request)
{
    // Tente d'authentifier l'utilisateur avec Sanctum
    $user = Auth::guard('sanctum')->user();

    // Si l'utilisateur est authentifié, retourne son ID
    if ($user) {
        return response()->json([
            'status' => 'success',
            'user_id' => $user->id, // Renvoie l'ID de l'utilisateur
        ], 200);
    } else {
        // Sinon, retourne une erreur pour un token invalide ou expiré
        return response()->json([
            'status' => 'error',
            'message' => 'Token invalide ou expiré',
        ], 401);
    }
}
 
}
