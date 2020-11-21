<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LiveGame extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'phase' => $this->phase,
            'dran' => $this->dran,
            'aktuellerStich' => $this->aktuellerStich,
            'letzterStich' => $this->letzterStich,
            'spielerIDs' => $this->spielerIDs,

        ];
    }
}
