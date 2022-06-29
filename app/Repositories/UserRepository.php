<?php


namespace App\Repositories;

use App\Models\User;
use Auth;

class UserRepository
{
    protected $mUser;

    public function __construct()
    {
        $this->mUser = new User();
    }

    public function getAll()
    {
        return $this->mUser->all();
    }

    public function findUser($id)
    {
        return $this->mUser
            ->with('permissions')
            ->with('roles')
            ->find($id);
    }

    public function showUser() {
        $user = Auth::user();
        $user->displayName = $user->name;
        return [
            'user' => $user,
            'roles' => $user->roles,
            'permissions' => $user->permissions
        ];
    }
}
