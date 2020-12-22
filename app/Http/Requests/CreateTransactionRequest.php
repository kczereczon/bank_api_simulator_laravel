<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
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
            'nrb_ben' => 'required',
            'name_ben' => 'required',
            'address_ben' => 'optional',
            'amount'=> 'required',
            'title'=> 'required',
            'nrb_prin'=> 'required',
            'name_prin'=> 'required',
            'realisation_date'=> 'required'
        ];
    }
}
