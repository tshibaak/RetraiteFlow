<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
class AuthController extends Controller
{
      public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if(Auth::attempt($credentials)) {
            $user = Auth::user();
            return redirect()->intended($this->route($user->role->name));
        }
    
       return redirect()->back()->withErrors(['error' => 'Email ou Mot de passe incorrect']);
    } 

    private function route(string $role):string{
        return match ($role) {
            'encadreur' => route('encadreur.index') ,
            'discipline' => route('discipline.index') ,
            'finance' => route('finance.index'),
            'admin' => route('admin.index'),
            'logistique' => route('logistique.index')
        };
    }

    public function logout(){
        Auth::logout();
        redirect(route('login'));
    }
}
