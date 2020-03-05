<?php

namespace App\Http\Requests\Api;

use App\Components\Entities\ApiRequest;
use Illuminate\Support\Facades\Route;

/**
 * Класс, представляющий запрос на добавление рейсов через API.
 * @package App\Http\Requests\Api Запросы, отправляемые через API.
 */
class FlightRequest extends ApiRequest
{
    /**
     * Возвращает флаг того, что это загрузка нескольких рейсов
     *
     * @return bool
     */
    protected function isMultiple()
    {
        // Проверяем загружен ли маршрут по добавлению нескольких рейсов
        return Route::currentRouteName() === 'load-flights';
    }

    /**
     * Возвращает правила валидации.
     *
     * @return array массив с правилами валидации.
     */
    public function rules()
    {
        // Если это добавление нескольких рейсов - выдаем правила с массивом
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
        // Массив правил валидации
        return [
            // Дата вылета
            'departure_datetime' => 'required|date_format:Y-m-d H:i:s',
            // Дата прилета
            'arrival_datetime' => 'required|date_format:Y-m-d H:i:s',
            // Номер рейса
            'number' => 'required',
            // Бортовой номер ВС
            'board' => 'required',
            // Аэропорт вылета
            'departure_airport' => 'required',
            // Аэропорт прилета
            'arrival_airport' => 'required',
            // КВС
            'captain' => 'required'
        ];
    }
}
