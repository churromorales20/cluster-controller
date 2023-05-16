<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
        ], 200);
    }

    public function userCheck(Request $request){
       $user = $request->user();
       return response()->json([
            'status' => 'success',
            'user_info' => $user,
            'initial_cofigurations' => ConfigurationsHelper::GetInitials(),
            'menu_items' => count($user->getRoleNames()) > 0 ? Role::where('name', $user->getRoleNames()[0])->first()->menues : [],
            'message' => 'User Logged In Successfully',
        ], 200);
    }
    /**
     * Create User
     * @param Request $request
     * @return User 
     */
    public function createUser(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make($request->all(), 
            [
                'name' => 'required',
                'username' => 'required|unique:users,username',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => 'error',
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password)
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request)
    {
        /*return response()->json([
            'status' => 'error',
            'message' => $request->input('email'),
        ]);*/
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'username' => 'required',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => 'error',
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            //dd('ABABBABB');\
            /*return response()->json([
                'status' => 'error',
                'message' => $request->only(['email', 'password'])
            ]);*/
            if(!Auth::attempt($request->only(['username', 'password']))){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }
            $user = User::where('email', $request->email)->first();
            //dd(count($user->getRoleNames()) > 0 ? Role::where('name', $user->getRoleNames()[0])->first()->menues : []);
            return response()->json([
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}