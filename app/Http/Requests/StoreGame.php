<?php

namespace App\Http\Requests;

use App\Models\Round;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;

class StoreGame extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->round());
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'winners' => 'required|max:3|array',
            'winners.*' => 'integer|distinct|exists:players,id',
            'points' => 'required|integer|between:-4,16',
            'misplayed' => 'boolean',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $activePlayerIds = $this->activePlayerIds();
            if (collect($this->input('winners'))->diff($activePlayerIds)->isNotEmpty()) {
                $validator->errors()->add('winners', 'Gewinnen kann nur, wer in diesem Spiel nicht aussetzt!');
            }

            if ($this->input('misplayed') && count($this->input('winners')) !== 3) {
                $validator->errors()->add('misplayed', 'Wenn sich jemand vergibt, muss dies als verlorenes Solo eingetragen werden!');
            }

            if ($this->input('misplayed') && $this->input('points') < 2) {
                $validator->errors()->add('points', 'Wenn sich jemand vergibt, verliert er mit mindestens 2 Punkten!');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'winners.required' => 'Es muss mindestens ein Gewinner ausgewählt werden!',
            'winners.max' => 'Es dürfen höchstens drei Gewinner ausgewählt werden!',
            'points.required' => 'Es muss eine Punktzahl angegeben werden!',
        ];
    }

    /**
     * @return Collection<int, int>
     */
    protected function activePlayerIds(): Collection
    {
        return $this->round()->getActivePlayers()->pluck('id');
    }

    protected function round(): Round
    {
        return $this->route('round');
    }
}
