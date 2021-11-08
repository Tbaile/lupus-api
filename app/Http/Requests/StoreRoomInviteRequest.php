<?php

namespace App\Http\Requests;

use App\Enums\RoomRoleEnum;
use Illuminate\Foundation\Http\FormRequest;

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
     * @return array
     */
    public function rules(): array
    {
        return [
            'users' => 'required|array|min:1',
            'users.*.id' => 'required|exists:App\Models\User,id',
            'users.*.role' => 'required|enum:'.RoomRoleEnum::class
        ];
    }
}
