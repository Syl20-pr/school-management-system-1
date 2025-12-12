<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BulkPromotionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
            'action' => 'required|in:promote,repeat,exclude',
            'target_class' => 'nullable|required_if:action,promote|exists:student_classes,id',
            'reason' => 'nullable|string|max:500'
        ];
    }
    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with(['alert-type' => 'error', 'message' => 'Veuillez corriger les erreurs ci-dessous.'])
        );
    }
}