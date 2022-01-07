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
            'is_with_ai' => $this->is_with_ai,
            'phase' => $this->phase,
            'dran' => $this->dran,
            'stichNr' => $this->stichNr,
            'vorhand' => $this->vorhand,
            'aktuellerStich' => $this->aktuellerStich,
            'letzterStich' => $this->letzterStich,
            'spieler' => $this->spieler,
            'messages' => $this->messages,
            'spieltyp' => $this->spieltyp,
            'schweinchen' => $this->schweinchen,
            'winners' => $this->when($this->beendet, $this->winners),
            'wertung' => $this->when($this->beendet, $this->wertung),
            'reAugen' => $this->when($this->beendet, $this->reAugen),
            'kontraAugen' => $this->when($this->beendet, $this->kontraAugen),
            'gewinntRe' => $this->when($this->beendet, $this->gewinntRe),
            'wertungsPunkte' => $this->when($this->beendet, $this->wertungsPunkte),
            'kontrasOffengelegt' => $this->kontrasOffengelegt,
            //'stiche' => $this->stiche,
            'reAbsage' => $this->reAbsage,
            'kontraAbsage' => $this->kontraAbsage,
            'reAnsage' => $this->reAnsage,
            'kontraAnsage' => $this->kontraAnsage,
            /*'spieler0' => $this->spieler0,
            'spieler1' => $this->spieler1,
            'spieler2' => $this->spieler2,
            'spieler3' => $this->spieler3,*/
        ];
    }
}
