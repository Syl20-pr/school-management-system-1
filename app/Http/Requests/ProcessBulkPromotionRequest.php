<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessBulkPromotionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'student_ids'   => 'required|array|min:1',
            'student_ids.*' => 'integer|exists:assign_students,student_id',
            'action'        => 'required|in:promote,repeat,exclude',
            'target_class'  => 'nullable|integer|exists:student_classes,id',
            'reason'        => 'nullable|string|max:500',
        ];
    }

    public function withValidator($validator)
{
    $validator->after(function ($v) {
        $action = $this->input('action');
        $targetClass = $this->input('target_class');

        // ✅ Promotion : target_class obligatoire
        if ($action === 'promote' && !$targetClass) {
            $v->errors()->add('target_class', 'La classe de destination est requise pour la promotion.');
        }

        // ✅ Repeat : target_class optionnel mais doit être du même niveau si fourni
        if ($action === 'repeat' && $targetClass) {
            // Cette vérification sera faite dans le service
        }

        // ✅ Exclude : target_class interdit
        if ($action === 'exclude' && $targetClass) {
            $v->errors()->add('target_class', 'La classe de destination ne doit pas être fournie pour une exclusion.');
        }
    });
}
}
