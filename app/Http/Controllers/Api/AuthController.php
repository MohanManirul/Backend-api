<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FileUpload;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function index()
    {
        $user = User::all();
        return send_response('Success !', $user);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

       if($validator->fails()) return send_error('Validation error', $validator->errors() , 422);
       
        // receive email & password for login
        $credentials = $request->only('email','password');
        if(Auth::attempt($credentials)){
            $user = Auth::user();
            $data['name'] = $user->name;
            $data['access_token'] = $user->createToken('accessToken')->accessToken;
            return send_response('You are successfully logged In' , $data);
        }else{
            return send_error('Unauthorized', 401);
        }
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
            // upload image
            if ($request->image) {
                if (File::exists('images/' . $user->image)) {
                    File::delete('images/' . $user->image);
                }
                $image = $request->file('image');
                $img = time() . Str::random(12) . '.' . $image->getClientOriginalExtension();
                $location = public_path('images/' . $img);
                Image::make($image)->save($location);
                $user->image = $img;
            }
            //upload image
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

    public function logout(Request $request)
    {
        auth()->user()->token()->revoke(); //remove the access token
        return response()->json(['message' => 'Successfully Loged Out']);

    }

    public function show($id)
    {
       $user = User::find($id);
       if($user){
        return send_response('Success !', $user);
       }else{
        return send_error('Data not found !');
       }
      
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "image" => "image",            
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {
            try {
                $user = User::find(auth()->user()->id);

                if ($request->image) {
                    if (File::exists('images/' . $user->image)) {
                        File::delete('images/' . $user->image);
                    }
                    $image = $request->file('image');
                    $img = time() . Str::random(12) . '.' . $image->getClientOriginalExtension();
                    $location = public_path('images/' . $img);
                    Image::make($image)->save($location);
                    $user->image = $img;
                }

                if ($user->save()) {
                    return response()->json(['success' => 'Profile Updated'], 200);
                }
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()], 200);
            }
        }
    }

    // imageUpload method start
    public function imageUpload(Request $request){
            $file_upload  =  new FileUpload();
            // upload image
            if ($request->image) {
                if (File::exists('images/' . $file_upload->image)) {
                    File::delete('images/' . $file_upload->image);
                }
                $image = $request->file('image');
                $img = time() . Str::random(12) . '.' . $image->getClientOriginalExtension();
                $location = public_path('images/' . $img);
                Image::make($image)->save($location);
                $file_upload->image = $img;
            }
            //upload image
            if ($file_upload->save()) {
                return response()->json(['success' => 'Profile Information Updated Successfully'], 200);
            }
    }
    // imageUpload method ends

}
