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
    
                $data = $this->create_error( 'El usuario no se ha creado',  $validate->errors(), 404);
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

            $data = $this->create_error( 'Los datos enviados no son correctos', 'error en los datos', 404);
        }
        
        
        // crear usuario

        return response()->json($data, $data['code']);
    }

    public function login(Request $request){

        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        // limpiar datos
        $params_array = array_map('trim', $params_array);

        // validar los datos
        if(!empty($params) && !empty($params_array) ){
            
            $validate = \Validator::make($params_array,[
                'email'     =>  'required|email',
                'password'  =>  'required',
            ]);
            // error en los datos
            if($validate->fails()){
    
                $data = $this->create_error('El usuario no se ha Logeado', $validate->errors(), 404);
            }
            // datos válidos 
            if(!$validate->fails()){

                $jwtAuth = new \JwtAuth();
                $data = $jwtAuth->signup($params->email, $params->password, $params->getToken);
            }
        }
        // error en los datos
        if(empty($params) || empty($params_array)){
            $data = $this->create_error('El usuario no se ha Logeado', 'datos vacíos', 404);
        }

        return $data;

    }

    public function create_error($msg, $error, $code = 404){
        return array(
            'status'    =>  'error',
            'code'      =>  $code,
            'message'   =>  $msg,
            'errors'    =>  $error
        );
    }
}

