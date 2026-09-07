<?php

namespace App\Services;

use App\Models\User;
use App\Helpers\FileUploader;

class UserService
{

    public function updateProfile($user, $data)
    {
        $user = User::find($user);
        if ($data->name) {
            $user->name = $data->name;
        }
        if ($data->phone) {
            $user->phone = $data->phone;
        }
        if ($data->email) {
            $user->email = $data->email;
        }
        if ($data->address) {
            $user->address = $data->address;
        }
        if ($data->dob) {
            $user->dob = date('Y-m-d', strtotime($data->dob));
        }
        if ($data->gender) {
            $user->gender = $data->gender;
        }
        if ($data->file('image')) {
            $user->image = FileUploader::uploadFile($data->file('image'), 'images/user');
        }
        $user->save();
        return $user;
    }
}
