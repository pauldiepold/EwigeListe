<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Interfaces\ExceptionInterface;
use Intervention\Image\Laravel\Facades\Image;

class UserAvatarController extends Controller
{

    public function store(Request $request, User $user)
    {
        $request->validate([
            'avatar' => 'required|string'
        ]);

        $this->authorize('update', $user);

        try {
            $image = Image::decodeDataUri($request->get('avatar'));
        } catch (ExceptionInterface) {
            throw ValidationException::withMessages(['avatar' => 'Das Bild konnte nicht gelesen werden.']);
        }

        $basename = date('Y-m-d_H-i-s_') . $user->player->surname . '_' . $user->player->name;

        Storage::disk('public')->put('avatars/' . $basename . '_full.jpg', (string) $image->encodeUsingFileExtension('jpg'));
        Storage::disk('public')->put('avatars/' . $basename . '.jpg', (string) $image->scale(width: 200)->encodeUsingFileExtension('jpg'));

        $user->update([
            'avatar_path' => $basename . '.jpg'
        ]);

        return response()->noContent();
    }
}
