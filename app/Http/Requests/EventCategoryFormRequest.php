<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
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
        return Gate::allows('manager');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'code' => 'required|unique:event_categories,code',
            'name' => 'required'
        ];

        // Если это администратор - добавляем проверку отдела (менеджеру отдел проставится автоматически)
        if(Gate::allows('admin')) {
            $rules['department_id'] = 'required|exists:departments,id';
        }

        if($this->has('category_id')) {
            // Игнорируем его ID (чтобы не работало правило unique на текущую категорию)
            $rules['code'] = [
                'required',
                Rule::unique('event_categories')->ignore($this->get('category_id'))
            ];
        }

        return $rules;
    }
}
