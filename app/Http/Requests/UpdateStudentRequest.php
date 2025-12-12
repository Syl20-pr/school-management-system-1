<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // Champs obligatoires
            'id'          => ['required', 'integer'], // assign_student id (hidden input)
            'name'        => ['required', 'string', 'max:255'],
            'gender'      => ['required', Rule::in(['Masculin', 'Féminin'])],
            'statusclass' => ['required', Rule::in(['N', 'D'])],
            'year_id'     => ['required', 'exists:student_years,id'],
            'class_id'    => ['required', 'exists:student_classes,id'],
            'group_id'    => ['required', 'exists:student_groups,id'],

            // Champs optionnels (peuvent être vides à la soumission)
            'fname'       => ['nullable', 'string', 'max:255'],
            'mname'       => ['nullable', 'string', 'max:255'],
            'mobile'      => ['nullable', 'string', 'max:30'],
            'address'     => ['nullable', 'string', 'max:255'],
            'religion'    => ['nullable', Rule::in(['Islam', 'Animiste', 'Chrétien', "S'Abstenir", 'Hindu'])],
            'dob'         => ['nullable', 'date'],
            'lob'         => ['nullable', 'string', 'max:255'],
            'f_no'        => ['nullable', 'string', 'max:30'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

            // Scolarité
            'shift_id'    => ['nullable', 'exists:student_shifts,id'], // Si c'est optionnel, on peut le rendre nullable ici

            // Frais
            'discount'    => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }
}

