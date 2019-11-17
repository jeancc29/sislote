<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlocksplaysgeneralsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'datos.idBloqueo' => 'required|exists:blocksplaysgenerals,id'
        ];
    }

    public function messages()
    {
        return [
            'datos.idBloqueo.required' => 'El bloqueo es requerido',
            'datos.idBloqueo.exists'  => 'El bloqueo no existe',
        ];
    }
}
