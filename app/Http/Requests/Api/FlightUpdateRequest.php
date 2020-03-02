<?php

namespace App\Http\Requests\Api;

use App\Components\Entities\ApiRequest;

class FlightUpdateRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'departure_datetime' => 'nullable|date_format:Y-m-d H:i:s',
            'arrival_datetime' => 'nullable|date_format:Y-m-d H:i:s'
        ];
    }
}
