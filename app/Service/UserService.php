<?php

namespace App\Service;

use App\Exceptions\UserEmailException;
use App\Http\Requests\UserCreateForm;
use App\Http\Resources\UserResource;
use App\Http\Resources\UsersCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserService
{

    /**
     * Create new user
     * 
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Check if email is valid and doesn't exists
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|unique:users,email',
                'password' => 'required',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'status'        => false,
                    'message'       => 'Email has already been registered. Please try again.'
                ],422);
            }

            $user                    = new User();

            $user->name               = $request->get('name');
            $user->email              = $request->get('email');
            $user->password           = Hash::make($request->get('password'));

            $user->save();

            DB::commit();

            return response()->json([
                'status'    => true,
                'message'   => 'New user added.',
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'    => false,
                'message'   => 'Failed to create new user. Please try again.',
                'e'         => $e,
            ], 400);
        }
    }

    /**
     * Update new user
     * 
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            // Check if email is valid and doesn't exists
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'name' => 'required',
                'email' => 'required|unique:users,email',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'status'        => false,
                    'message'       => 'Invalid validation. Please try again.'
                ],422);
            }

            $user = User::find($id);

            if(is_null($user)) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'User detail not found. Please try again.'
                ], 404);
            }

            $user->id                = $request->get('id');
            $user->name              = $request->get('name');
            $user->email             = $request->get('email');
            $message                 = 'User successfully updated.';

            $user->save();

            DB::commit();

            return response()->json([
                'status'        => true,
                'message'       => $message,
                'data'          => [
                    'user'      => new UserResource($user)
                ]
            ], 200);

        }catch (\Exception $e) {
            DB::rollback();

            // throw new UserUpdateException();
            return response()->json([
                'status'    => false,
                'message'   => 'Failed to update user. Please try again.',
                'e'         => $e,
            ], 400);
        }
    }

}