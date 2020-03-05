<?php

namespace App\Http\Requests\Api;

use App\Components\Entities\ApiRequest;

/**
 * Класс, представляющий запрос на обновление рейсов через API.
 * @package App\Http\Requests\Api Запросы, отправляемые через API.
 */
class FlightUpdateRequest extends ApiRequest
{
    /**
     * Возвращает правила валидации.
     *
     * @return array массив с правилами валидации.
     */
    public function rules()
    {
        // Объявляем только два правила, относящиеся к формату данных. Так можно обновлять части модели.
        return [
            // дата вылета
            'departure_datetime' => 'nullable|date_format:Y-m-d H:i:s',
            // дата прилета
            'arrival_datetime' => 'nullable|date_format:Y-m-d H:i:s'
        ];
    }
}
