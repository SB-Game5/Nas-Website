<?php
namespace App\Http\Controllers;

use App\Http\Requests\AddUserRequest;
use App\DTOs\UserDTO;
use App\Actions\AddUserAction;
use App\Http\Requests\DeleteUserRequest;
use App\DTOs\DeleteUserDTO;
use App\Actions\DeleteUserAction;
use App\Actions\ListUserAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function create()
    {
        return view('Onglets/Users/AddUser');
    }    
    
    public function store(AddUserRequest $request, AddUserAction $addUserAction)
    {
        $dto = UserDTO::fromRequest($request);
        try {
            $addUserAction->execute($dto);
            return response()->noContent(); //when success
                             
            } catch (\Exception $e) {
            
            return response()->json(['error' => $e->getMessage()], 500); //when error
        }
    }
    public function remove(DeleteUserRequest $request, DeleteUserAction $deleteUserAction)
    {
        $dto = DeleteUserDTO::fromRequest($request);
        try {
            $deleteUserAction->execute($dto);
            return redirect()->route('dashboard')->with('success', 'Utilisateur supprimé  !'); 
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->withErrors(['error' => $e->getMessage()]);
        }
    }
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('login');
    }

    public function login(Request $request, ListUserAction $listUserAction)
    {
        $credentials = $request->validate([
            'username' => 'required|string|min:4|max:50',
            'password' => 'required|string|min:4',
        ]);

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['username' => 'Identifiants incorrects'])
                ->withInput();
        }
        try {
            $users = $listUserAction->execute();
            
            // ÉTAPE CRUCIALE : On extrait uniquement les pseudos du tableau multidimensionnel
            $usernamesOnNas = array_column($users, 'username');

            if (! in_array($credentials['username'], $usernamesOnNas, true)) {
                Auth::logout();

                return back()
                    ->withErrors(['username' => 'Ce compte n\'est pas reconnu sur le NAS'])
                    ->withInput();
            }
        } catch (\Exception $e) {
            // Si la connexion SSH échoue pendant le login, on déconnecte par sécurité
            Auth::logout();
            return back()
                ->withErrors(['username' => 'Erreur de communication avec le NAS : ' . $e->getMessage()])
                ->withInput();
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}