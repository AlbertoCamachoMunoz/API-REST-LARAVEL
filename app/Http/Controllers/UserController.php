<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function pruebas(Request $request){
        return "Accion de Controller";
    }

    public function register(Request $request){

        // recoger los datos del usuario por post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        // var_dump($params);
        // var_dump($params_array);
        // die();

        // limpiar datos
        $params_array = array_map('trim', $params_array);
        
        // validar los datos
        if(!empty($params) && !empty($params_array) ){
            
            $validate = \Validator::make($params_array,[
                'name'      =>  'required|alpha',
                'surname'   =>  'required|alpha',
                'email'     =>  'required|email|unique:users',
                'password'  =>  'required',
            ]);
    
            if($validate->fails()){
    
                $data = array(
                    'status'    =>  'error',
                    'code'      =>  404,
                    'message'   =>  'El usuario no se ha creado',
                    'errors'   =>  $validate->errors()
                );
            }
            // validacion correcta
            if(!$validate->fails()){

                // cifrar la contraseña
                $pwd = password_hash($params->password, PASSWORD_BCRYPT, ['cost' => 4]);

                // creamos el usuario
                $user = new User();
                $user->name = $params->name;
                $user->surname = $params->surname;
                $user->role = 'ROLE_USER';
                $user->email = $params->email;
                $user->password = $pwd;

                // Guardamos el usuario
                $user->save();

                $data = array(
                    'status'    =>  'ok',
                    'code'      =>  200,
                    'message'   =>  'El usuario SI se ha creado',
                    'user'      =>  $user
                );
            }
        }

        if(empty($params) && empty($params_array) ){
            $data = array(
                'status'    =>  'error',
                'code'      =>  404,
                'message'   =>  'Los datos enviados no son correctos'
            );
        }
        

        

        // crear usuario




        return response()->json($data, $data['code']);
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

