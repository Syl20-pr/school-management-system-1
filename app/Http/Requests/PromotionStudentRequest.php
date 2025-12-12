<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromotionStudentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'        => ['required','string','max:255'],
            'fname'       => ['nullable','string','max:255'], // Changé à nullable
            'mname'       => ['nullable','string','max:255'], // Changé à nullable
            'mobile'      => ['nullable','string','max:30'],  // Changé à nullable
            'address'     => ['nullable','string','max:255'], // Changé à nullable
            'gender'      => ['required', Rule::in(['Masculin','Féminin'])],
            'religion'    => ['nullable', Rule::in(['Islam','Animiste','Chrétien',"S'Abstenir",'Hindu'])], // Changé à nullable
            'dob'         => ['nullable','date'], // Changé à nullable
            'lob'         => ['nullable','string','max:255'], // Changé à nullable
            'f_no'        => ['nullable','string','max:30'], // Changé à nullable
            'image'       => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],

            'year_id'     => ['required','exists:student_years,id'],
            'class_id'    => ['required','exists:student_classes,id'],
            'group_id'    => ['required','exists:student_groups,id'],
            'shift_id'    => ['required','exists:student_shifts,id'],

            'discount'    => ['nullable','numeric','min:0','max:100'],
        ];
    }
}