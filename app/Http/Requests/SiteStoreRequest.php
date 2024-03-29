<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SiteStoreRequest extends FormRequest
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
            'name' => ['required', 'max:255', 'string'],
            'year' => ['required', 'max:255'],
            'path_nl' => ['nullable', 'mimes:zip'],
            'path_en' => ['nullable', 'mimes:zip'],
            'allow_unsafe' => ['boolean'],
        ];
    }
}
