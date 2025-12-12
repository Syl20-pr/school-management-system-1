<?php

namespace App\Http\Requests\Timetable;
use Illuminate\Foundation\Http\FormRequest;

class GenerateTimeTableRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'academic_year_id' => 'required|exists:student_years,id',
            'class_id' => 'required|exists:student_classes,id',
            'term' => 'required|in:first,second,third',
            'max_hours_per_teacher' => 'required|integer|min:1|max:40',
            'preferred_subject_times' => 'sometimes|array',
            'teacher_constraints' => 'sometimes|array',
        ];
    }

    public function messages()
    {
        return [
            'academic_year_id.required' => 'L\'année académique est requise.',
            'class_id.required' => 'La classe est requise.',
            'max_hours_per_teacher.required' => 'Le nombre maximum d\'heures par enseignant est requis.',
            'max_hours_per_teacher.min' => 'Le nombre maximum d\'heures doit être d\'au moins 1.',
            'max_hours_per_teacher.max' => 'Le nombre maximum d\'heures ne peut pas dépasser 40.',
        ];
    }
}
