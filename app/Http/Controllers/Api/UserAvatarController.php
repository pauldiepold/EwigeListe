<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class UserAvatarController extends Controller
{

    public function store(Request $request, User $user)
    {
        $request->validate([
            'avatar' => 'required|string'
        ]);

        $this->authorize('update', $user);

        $filename = date('Y-m-d_H-i-s_') . $user->player->surname . '_' . $user->player->name . '.jpg';
        $filename_full = date('Y-m-d_H-i-s_') . $user->player->surname . '_' . $user->player->name . '_full.jpg';
        $path = public_path('storage/avatars/') . $filename;
        $path_full = public_path('storage/avatars/') . $filename_full;

        Image::make($request->get('avatar'))->save($path_full);

        Image::make($request->get('avatar'))->resize(200, null, function ($constraint)
        {
            $constraint->aspectRatio();
        })->save($path);

        $user->update([
            'avatar_path' => $filename
        ]);

        return response(204);
    }
}
