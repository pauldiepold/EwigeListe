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
            'vorhand' => $this->vorhand,
            'aktuellerStich' => $this->aktuellerStich,
            'letzterStich' => $this->letzterStich,
            'spieler' => $this->spieler,
            'wertung' => $this->wertung,
            'messages' => $this->messages,
            'spieltyp' => $this->spieltyp,
            'spieler0' => $this->spieler0,
            'spieler1' => $this->spieler1,
            'spieler2' => $this->spieler2,
            'spieler3' => $this->spieler3,
        ];
    }
}
