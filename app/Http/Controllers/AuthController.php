<?php

namespace App\Http\Controllers;

use App\Events\NotifyUser;
use App\Models\User;
use App\Notifications\UserRegisteredNotification;
// use App\Notifications\MyNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            // 'c_password' => 'required|same:password',
        ]);
   
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }
        
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['first_name'] =  $user->first_name;
        $success['last_name'] =  $user->last_name;
   
        // return $this->sendResponse($success, 'User register successfully.');
        return response()->json([
            'success' => true,
            'message' => 'User registered successfully.'
        ], 200); 
    }

    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->plainTextToken; 
            $success['name'] =  $user->name;
            
            // // Broadcast a notification
            // broadcast(new NotifyUser('Welcome to the application!'));
            // After user creation
            // $user->notify(new NotifyUser($user));
            // $user->notify(new UserRegisteredNotification($user));

            try {
                $user->notify(new UserRegisteredNotification($user));
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return response()->json([
                'success' => true,
                'token' => $success['token'],
                'message' => 'Login successful.'
            ], 200); 
        } 
        else{ 
            return response()->json([
                'success' => false,
                'message' => 'Wrong Credentials.',
                'data' => $request->email
            ], 401); // 422 Unprocessable Entity
        } 
    }

    public function logout(Request $request): JsonResponse
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'token deleted successfully',
            'data' => null,
        ]);
    }

    public function getNotifications(Request $request)
    {
        $user = $request->user();
        return response()->json($user->notifications);
    }
}
