<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function pruebas(Request $request){
        return "Accion de Controller";
    }

    public function register(Request $request){
        return "Accion de register";
    }

    public function login(Request $request){
        $name = $request->input('name');
        $surname = $request->input('surname');

        return array(
            $name,
            $surname
        );
    }
}
