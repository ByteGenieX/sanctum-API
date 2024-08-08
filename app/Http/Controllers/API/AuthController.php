<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    
    public function test(){
        echo "hi this is API";
    }

    public function signup(Request $request){

        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'password' => 'required|min:8',
                'email' => 'required|email|unique:users'
            ]
        );
        
      
        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'message'=>'Validation Error',
                'error'=>$validator->errors()->all()
            ],401);
        }
        
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->password
            // 'password'=>bcrypt($request->password)
        ]);        

        return response()->json([
            'status'=>true,
            'message'=>'User created Successfully',
            'user'=>$user,           
        ],200);
    }
    
    public function login(Request $request){

        $validateUser=Validator::make($request->all(),[
           
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if($validateUser->fails()){
            return response()->json([
                'status'=>false,
                'message'=>'Authontication Faield',
                'error'=>$validateUser->errors()->all()
            ],404);
        }
            
        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
            $authUser = Auth::User();    
                return response()->json([
                        'status'=>true,
                        'message'=>'User Logged in Successfully',
                        'token'=> $authUser->createToken("API Token")->plainTextToken,
                        'token_type'=>'beare'
                ],200);
        }else{
                return response()->json([
                    'status'=>false,
                    'message'=>'Email or Password does not matched'                    
                ],401);
        }
                
    }
    
    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
                'status'=>true,
                'message'=>'Logout Successfully',
                'user'=>$user
                ],200);        

    }
}
