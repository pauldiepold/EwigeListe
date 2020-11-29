<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LiveRound extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'schweinchen' => $this->schweinchen,
            'fuchsSticht' => $this->fuchsSticht,
            'schweinchenTrumpfsolo' => $this->schweinchenTrumpfsolo,
            'koenigsSolo' => $this->koenigsSolo,
            'karlchen' => $this->karlchen,
            'karlchenFangen' => $this->karlchenFangen,
        ];
    }
}
