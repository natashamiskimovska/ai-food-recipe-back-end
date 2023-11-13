<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
        $data['password'] = Hash::make($data['password']);
        $data['remember_token'] = Str::random(10);
        $data['type'] = $data['type'] ?: 0;

        $user = User::create($data);
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;

        return [
            'token' => $token
        ];
    }

    /**
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $user->refresh();
    }

    public function index()
    {
        return User::all();
    }
}
