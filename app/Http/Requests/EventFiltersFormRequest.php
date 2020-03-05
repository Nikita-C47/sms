<?php

namespace App\Http\Requests;

use App\Models\Events\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EventFiltersFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $statuses = array_keys(Event::EVENT_STATUSES);

        return [
            'date_from' => 'nullable|date_format:d.m.Y|before_or_equal:date_to',
            'date_to' => 'nullable|date_format:d.m.Y|after_or_equal:date_to|before_or_equal:'.now()->format('d.m.Y'),
            'boards' => 'nullable|array',
            'boards.*' => 'exists:flights,board',
            'captains' => 'nullable|array',
            'captains.*' => 'exists:flights,captain',
            'airports' => 'nullable|array',
            'airports.*' => 'exists:events,airport',
            'statuses' => 'nullable|array',
            'statuses.*' => 'in:'.implode(",", $statuses),
            'responsible_departments' => 'nullable|array',
            'responsible_departments.*' => 'exists:departments,id',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'relations' => 'nullable|array',
            'relations.*' => 'exists:event_relations,id',
            'attachments' => 'nullable|boolean'
        ];
    }
}
