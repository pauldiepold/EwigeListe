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
            'avatar' => 'required'
        ]);

        $this->authorize('update', $user);

        $filename =  date('Y-m-d_H-i-s_') . $user->player->surname . '_' . $user->player->name . '.jpg';
        $path = public_path('storage/avatars/') . $filename;

        Image::make($request->get('avatar'))->save($path);

        $user->update([
            'avatar_path' => $filename
        ]);

        return response(204);
    }
}
