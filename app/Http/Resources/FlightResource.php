<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Класс, представляющий API-ресурс рейса.
 * @package App\Http\Resources API-ресурсы приложения.
 */
class FlightResource extends JsonResource
{
    /**
     * Конвертирует ресурс в массив.
     *
     * @param \Illuminate\Http\Request $request объект запроса.
     * @return array массив с информацией о рейсе.
     */
    public function toArray($request)
    {
        return [
            // ID рейса
            'id' => $this->id,
            // дата вылета
            'departure_datetime' => $this->departure_datetime->toDateTimeString(),
            // Дата прилета
            'arrival_datetime' => $this->arrival_datetime->toDateTimeString(),
            // Номер рейса
            'number' => $this->number,
            // Номер борта
            'board' => $this->board,
            // Код ВС
            'aircraft_code' => $this->aircraft_code,
            // Аэропорт вылета
            'departure_airport' => $this->departure_airport,
            // Аэропорт прилета
            'arrival_airport' => $this->arrival_airport,
            // КВС
            'captain' => $this->captain,
            // Второй КВС
            'extra_captain' => $this->extra_captain,
            // Когда добавлено
            'created_at' => $this->created_at->toDateTimeString(),
            // Когда обновлено
            'updated_at' => $this->updated_at->toDateTimeString()
        ];
    }
}
