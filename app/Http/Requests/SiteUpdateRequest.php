<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SiteUpdateRequest extends FormRequest
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
            'path_nl' => ['max:255', 'nullable', 'mimes:zip'],
            'path_en' => ['nullable', 'max:255', 'mimes:zip'],
            'allow_unsafe' => ['boolean'],
            'deactivate' => ['boolean'],
        ];
    }
}
