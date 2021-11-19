<?php

namespace App\Http\Requests;

use App\Models\Room;
use http\Exception\InvalidArgumentException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function rules(Request $request): array
    {
        /** @var Room $room */
        $room = $request->route('room');
        if ($room == null) {
            abort(422);
        }
        return [
            'users' => 'required|array|min:1',
            'users.*' => [
                'required',
                'exists:App\Models\User,id',
                Rule::in($room->users->pluck('id'))
            ]
        ];
    }
}
