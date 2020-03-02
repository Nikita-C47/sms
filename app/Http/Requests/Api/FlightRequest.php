<?php

namespace App\Http\Requests\Api;

use App\Components\Entities\ApiRequest;
use Illuminate\Support\Facades\Route;

class FlightRequest extends ApiRequest
{
    protected function isMultiple()
    {
        return Route::currentRouteName() === 'load-flights';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if($this->isMultiple()) {
            return [
                'flights' => 'array',
                'flights.*.departure_datetime' => 'required|date_format:Y-m-d H:i:s',
                'flights.*.arrival_datetime' => 'required|date_format:Y-m-d H:i:s',
                'flights.*.number' => 'required',
                'flights.*.board' => 'required',
                'flights.*.departure_airport' => 'required',
                'flights.*.arrival_airport' => 'required',
                'flights.*.captain' => 'required'
            ];
        }

        return [
            'departure_datetime' => 'required|date_format:Y-m-d H:i:s',
            'arrival_datetime' => 'required|date_format:Y-m-d H:i:s',
            'number' => 'required',
            'board' => 'required',
            'departure_airport' => 'required',
            'arrival_airport' => 'required',
            'captain' => 'required'
        ];
    }
}
