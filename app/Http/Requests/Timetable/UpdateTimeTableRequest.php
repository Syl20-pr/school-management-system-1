<?php

namespace App\Http\Requests\Timetable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTimeTableRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $timetableId = $this->route('timetable')->id;

        return [
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:student_years,id',
            'class_id' => 'required|exists:student_classes,id',
            'term' => 'required|in:first,second,third',
            'slots' => 'sometimes|array',
            'slots.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'slots.*.period_id' => 'required|exists:periods,id',
            'slots.*.subject_id' => 'required|exists:school_subjects,id',
            'slots.*.teacher_id' => 'required|exists:users,id',
            'slots.*.classroom_id' => 'required|exists:classrooms,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom de l\'emploi du temps est requis.',
            'academic_year_id.required' => 'L\'année académique est requise.',
            'class_id.required' => 'La classe est requise.',
            'slots.*.day_of_week.required' => 'Le jour de la semaine est requis pour chaque créneau.',
            'slots.*.period_id.required' => 'La période est requise pour chaque créneau.',
            'slots.*.subject_id.required' => 'La matière est requise pour chaque créneau.',
            'slots.*.teacher_id.required' => 'L\'enseignant est requis pour chaque créneau.',
            'slots.*.classroom_id.required' => 'La salle de classe est requise pour chaque créneau.',
        ];
    }
}