<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Entity\User;
use Hash;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();
        $name = $request->name;
        $email = $request->email;
        $sortField = $request->input("sorted", "id");
        $direction = $request->input("direction", "asc");
        if ($name) {
            $query->where("name", "like", "%" . $name . "%");
        }
        if ($email) {
            $query->where("email", "like", "%" . $email . "%");
        }
        $users = $query->orderBy($sortField, $direction)->paginate(10);
        return inertia("Users/Index", ["users" => UserResource::collection($users), "nameQuery" => $name, "emailQuery" => $email, "sortField" => $sortField, "direction" => $direction,"success"=>session("success")]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia("Users/Create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data["password"] = Hash::make($data["password"]);
        User::create($data);
        return to_route("user.index")->with("success","User Created Successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return inertia("Users/Edit",$user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $user->update($data);
        return to_route("user.index")->with("success", "User Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $name = $user->name;
        $user->delete();
        return to_route("user.index")->with("success", "User \" $name \" Deleted Successfully");
    }
}
