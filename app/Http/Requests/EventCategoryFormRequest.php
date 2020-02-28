<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EventCategoryFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var \App\User $user */
        $user = Auth::user();
        return $user->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'department_id' => 'required|exists:departments,id',
            'code' => 'required|unique:event_categories,code',
            'name' => 'required'
        ];

        if($this->has('category_id')) {
            // Игнорируем его ID (чтобы не работало правило unique на текущего пользователя)
            $rules['code'] = [
                'required',
                Rule::unique('event_categories')->ignore($this->get('category_id'))
            ];
        }

        return $rules;
    }
}
