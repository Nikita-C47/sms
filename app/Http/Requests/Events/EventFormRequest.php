<?php

namespace App\Http\Requests\Events;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Validator;
use function foo\func;

class EventFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if($this->has('event_id')) {
            return Gate::allows('manager');
        }

        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'date' => 'required|date_format:d.m.Y|before_or_equal:'.now()->format('d.m.Y'),
            'flight_id' => 'required_with:flight_connection|exists:flights,id',
            'relation_id' => 'nullable|exists:event_relations,id',
            'department_id' => 'nullable|exists:departments,id',
            'category_id' => 'nullable|exists:event_categories,id',
            'type_id' => 'nullable|exists:event_types,id',
            'attachments' => 'array',
            'attachments.*' => 'file',
            'message' => 'required'
        ];

        if($this->has('event_id')) {
            $rules['status'] = 'required|in:new,fixed,not_fixed';
            $rules['approved'] = 'nullable|boolean';
            $rules['fix_date'] = 'nullable|date_format:d.m.Y|before_or_equal:'.now()->format('d.m.Y') . '|after_or_equal:'.$this->get('date');
            $rules['responsible_departments'] = 'array';
            $rules['responsible_departments.*'] = 'exists:departments,id';

            for($i = 0; $i < $this->get('measures_count'); $i++) {
                $rules['measure_'.$i] = 'required';
            }

            for($i = 0; $i < $this->get('removed_measures_count'); $i++) {
                $rules['removed_measure_'.$i] = 'required|exists:event_measures,id';
            }

            for($i = 0; $i < $this->get('removed_attachments_count'); $i++) {
                $rules['removed_attachment_'.$i] = 'required|exists:event_attachments,id';
            }
        }

        return $rules;
    }
}
