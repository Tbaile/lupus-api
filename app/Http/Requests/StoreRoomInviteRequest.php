<?php

namespace App\Http\Requests;

use App\Enums\RoomRoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreRoomInviteRequest extends FormRequest
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
        /** @var \App\Models\Room $room */
        $room = $request->route('room');
        return [
            'users' => 'required|array|min:1',
            'users.*.id' => [
                'required',
                'numeric',
                'exists:App\Models\User,id',
                Rule::notIn($room->users()->pluck('id')->toArray())
            ],
            'users.*.role' => 'required|enum:'.RoomRoleEnum::class
        ];
    }
}
