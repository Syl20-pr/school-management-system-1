<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class ProcessPromotionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'action'       => 'required|in:promote,repeat,exclude,custom',
            'target_class' => 'nullable|integer|exists:student_classes,id',
            'reason'       => 'nullable|string|max:500',
        ];
    }

    /*public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $action = $this->input('action');
            $targetClass = $this->input('target_class');

            // ✅ Promote et Custom : target_class obligatoire
            if (in_array($action, ['promote','custom']) && !$targetClass) {
                $v->errors()->add('target_class', 'La classe de destination est requise pour cette action.');
            }

            // ✅ Repeat : target_class optionnel (si vide => même classe)
            if ($action === 'repeat' && $targetClass) {
                // ici tu pourrais ajouter une vérification sur le niveau si nécessaire
            }

            // ✅ Exclude : target_class interdit
            if ($action === 'exclude' && $targetClass) {
                $v->errors()->add('target_class', 'La classe de destination ne doit pas être fournie pour une exclusion.');
            }
        });
    }*/
    
    public function withValidator($validator)
{
    $validator->after(function ($v) {
        $action = $this->input('action');
        $targetClass = $this->input('target_class');

        // Promote et Custom : obligatoire
        if (in_array($action, ['promote','custom']) && !$targetClass) {
            $v->errors()->add('target_class', 'La classe de destination est requise pour cette action.');
        }

        // Repeat : obligatoire aussi
        if ($action === 'repeat' && !$targetClass) {
            $v->errors()->add('target_class', 'La classe de destination est requise pour le redoublement.');
        }

        // Exclude : interdit
        if ($action === 'exclude' && $targetClass) {
            $v->errors()->add('target_class', 'La classe de destination ne doit pas être fournie pour une exclusion.');
        }
    });
}

protected function failedValidation(Validator $validator)
{
    throw new HttpResponseException(
        redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with(['alert-type' => 'error', 'message' => 'Validation échouée pour la promotion en masse.'])
    );
}

}
