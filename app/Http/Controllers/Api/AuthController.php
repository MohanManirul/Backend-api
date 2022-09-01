<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

       if($validator->fails()) return send_error('Validation error', $validator->errors() , 422);
       

    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

       if($validator->fails()) return send_error('Validation error', $validator->errors() , 422);
       

       try{

            $user  =  new User();
            $user->name  = $request->name;
            $user->email  = $request->email;
            $user->password  = Hash::make($request->password);
            
            if($user->save()){
                $data = [
                    'name' => $user->name ,
                    'email' => $user->email 
                ];
                return send_response('User registration success !', $data);
            }           

        } catch( Exception $e){
            return send_error($e->getMessage(),$e->getCode());
                
        }
    }

}
