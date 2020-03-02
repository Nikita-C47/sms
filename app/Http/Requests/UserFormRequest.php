<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UserFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'access_level' => 'required|in:'.implode(',', array_keys(User::USER_ROLES)),
            'department_id' => 'nullable|exists:departments,id'
        ];

        if($this->has('user_id')) {
            $rules['email'] = [
                'required',
                'email',
                Rule::unique('users')->ignore($this->get('user_id'))
            ];
        }

        return $rules;
    }
}
