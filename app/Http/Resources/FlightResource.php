<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FlightResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'departure_datetime' => $this->departure_datetime->toDateTimeString(),
            'arrival_datetime' => $this->arrival_datetime->toDateTimeString(),
            'number' => $this->number,
            'board' => $this->board,
            'aircraft_code' => $this->aircraft_code,
            'departure_airport' => $this->departure_airport,
            'arrival_airport' => $this->arrival_airport,
            'captain' => $this->captain,
            'extra_captain' => $this->extra_captain,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString()
        ];
    }
}
