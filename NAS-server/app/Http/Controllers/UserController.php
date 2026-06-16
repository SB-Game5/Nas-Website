<?php
namespace App\Http\Controllers;

use App\Http\Requests\AddUserRequest;
use App\DTOs\UserDTO;
use App\Actions\AddUserAction;

class UserController extends Controller
{
    public function create()
    {
        return view('AddUser');
    }    
    
    public function store(AddUserRequest $request, AddUserAction $addUserAction)
    {
        $dto = UserDTO::fromRequest($request);

        try {
            $addUserAction->execute($dto);
            
            // Sécurisé : On force le retour au formulaire d'affichage
            return redirect()->route('users.create')->with('success', 'Utilisateur créé  !');
        } catch (\Exception $e) {
            // Sécurisé : On force le retour au formulaire avec l'erreur SSH
            return redirect()->route('users.create')->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
}