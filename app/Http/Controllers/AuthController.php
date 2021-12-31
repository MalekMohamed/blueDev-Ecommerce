<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Auth;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware("auth:api", ["except" => ["login",'verification_confirmation','resetPassword','SendResetPasswordMail']]);
    }

    public function logout()
    {
        $user = Auth::guard("api")->user()->token();
        $user->revoke();
        $responseMessage = "successfully logged out";
        return response()->json([
            'success' => true,
            'message' => $responseMessage
        ], 200);

    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|min:6',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ], 500);
        }

        $credentials = $request->only(["email", "password"]);
        $user = User::where('email', $credentials['email'])->first();
        if ($user) {
            if (!auth()->attempt($credentials)) {
                $responseMessage = "Invalid username or password";

                return response()->json([
                    "success" => false,
                    "message" => $responseMessage,
                    "error" => $responseMessage
                ], 422);
            }

            $accessToken = auth()->user()->createToken('authToken')->accessToken;
            $responseMessage = "Login Successful";

            return response()->json([
                'success' => (bool)$accessToken,
                'token' => $accessToken,
                'message' => $responseMessage,
                'user' => auth()->user()]);
        } else {
            $responseMessage = "Sorry, this user does not exist";
            return response()->json([
                "success" => false,
                "message" => $responseMessage,
                "error" => $responseMessage
            ], 422);
        }

    }
    public function updateUserData(Request $request){
        $user = User::findorfail(auth('api')->user()->id);
        $validator =Validator::make($request->all(), [
            'name' => 'string',
            'email' => 'string|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->messages()->toArray()
            ], 500);
        }
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->update($request->all());
        return response()->json([
            'status' => (bool)$user,
            'data' => $user,
            'message' => $user ? 'User Updated' : 'Error updating User']);
    }
    public function resendVerificationMail()
    {
        $user = auth('api')->user();
        $user->sendEmailVerificationNotification();
            return response()->json([
                'status' => true,
                'data' => auth('api')->user(),
                'message' => 'Verification email has been sent.'
            ]);
    }
    public function verification_confirmation($code){
        $user = User::where('verification_code', $code)->first();
        if($user != null){
            $user->email_verified_at = Carbon::now();
            $user->save();
//            return response()->json([
//                'success' => (bool)$user,
//                'message' => 'Email Verified Successfully',
//            ]);
            return redirect()->route('rootRoute','login');
        }
        else {
//            return response()->json([
//                'success' => (bool)$user,
//                'message' => 'Sorry, we could not verify your account. Please try again',
//            ]);
            return redirect()->route('rootRoute','login');
        }
    }
    public function SendResetPasswordMail(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ], 500);
        }
        Password::sendResetLink(
            $request->only('email')
        );
        return response()->json([
            'success' => 'success',
            'message' => 'We have sent you an mail. Please check your email'
        ], 200);
    }
    public function resetPassword(Request $request) {
       $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ], 500);
        }
        $reset_password_status = Password::reset($request->all(), function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json(['success' => false,"message" => "Invalid token provided"], 400);
        }

        return response()->json(['success' => false,"message" => "Password has been successfully changed"]);
    }
}
