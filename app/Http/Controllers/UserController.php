<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only(['update']);
        $this->middleware('isAdmin')->only(['destroy','index','show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::with(['products'])->get();
        return response()->json([
            'status' => (bool)$users,
            'data' => $users,
            'message' => $users ? '' : 'Error getting Users'
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::findorfail($id)->with(['products'])->first();
        return response()->json([
            'status' => (bool)$user,
            'data' => $user,
            'message' => $user ? '' : 'Error getting User'
        ]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'in:Admin,Customer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ], 500);
        }

        $user = new User();
        $user->fill($request->except('password'));
        $user->password = Hash::make($request->password);
        $user->sendEmailVerificationNotification();
        $user->save();
        return response()->json($user, 200);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::findorfail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'email' => 'string|unique:users,email,' . $id,
            'password' => 'min:6|confirmed',
            'role' => 'in:Admin,Customer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->messages()->toArray()
            ], 500);
        }
        $user->update($request->except('password'));
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        return response()->json([
            'status' => (bool)$user,
            'data' => $user,
            'message' => $user ? 'User Updated' : 'Error updating User']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::findorfail($id);
        $user->delete();
        return response()->json($user);
    }


}
