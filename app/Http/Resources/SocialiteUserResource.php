<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialiteUserResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        $nameParts = explode(' ', (string) $this->name, 2);

        return [
            'id' => $this->id,
            'email' => $this->email,
            'provider' => $this->provider,
            'surname' => $nameParts[0] ?? '',
            'name' => $nameParts[1] ?? '',
        ];
    }
}
