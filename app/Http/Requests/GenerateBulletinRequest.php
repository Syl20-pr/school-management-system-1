<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateBulletinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'year_id' => 'required|exists:student_years,id',
            'class_id' => 'required|exists:student_classes,id',
            'term_type_id' => 'required|exists:term_types,id',
        ];
    }

    public function messages()
    {
        return [
            'year_id.required' => 'La sélection de l\'année scolaire est obligatoire',
            'class_id.required' => 'La sélection de la classe est obligatoire',
            'term_type_id.required' => 'La sélection du type de bulletin est obligatoire',
        ];
    }

}
