<?php

namespace App\Components\Helpers;

use App\Models\Events\EventFilter;
use Illuminate\Support\Facades\Auth;

/**
 * Класс-помощник для работы с пользовательскими фильтрами списка событий.
 * @package App\Components\Helpers Классы-помощники.
 */
class EventFiltersHelper
{
    /** @var array $flightsColumns фильтры, которые относятся к таблице с рейсами.  */
    protected $flightsColumns = [
        'boards',
        'captains',
    ];
    /** @var array $responsibleDepartmentsColumns фильтры, которые относятся к таблице с ответственными подразделениями. */
    protected $responsibleDepartmentsColumns = [
        'responsible_departments'
    ];
    /** @var array $filters массив с фильтрами для текущего пользователя. */
    public $filters = [];
    /** @var bool $joinFlights флаг того, что нужно выполнить join таблицы с рейсами. */
    public $joinFlights = false;
    /** @var bool $joinResponsibleDepartments флаг того, что нужно выполнить join таблтцы с ответственными подразделениями. */
    public $joinResponsibleDepartments = false;

    /**
     * Создает новый экземпляр класса.
     */
    public function __construct()
    {
        // Сразу инициализируем фильтры
        $this->filters = $this->getFilters();
    }

    /**
     * Получает фильтры списка событий для текущего пользователя.
     *
     * @return array массив с фильтрами.
     */
    public function getFilters()
    {
        /** @var \App\User $user */
        $user = Auth::user();
        // Получаем фильтры текущего пользователя и группируем их сначала по ключу, потом по значению
        $filters = $user->event_filters->groupBy(['key', 'value'])->toArray();
        // Тут будет результат
        $result = [];
        // Перебираем фильтры
        foreach ($filters as $key => $values) {
            // Перебираем значения фильтров
            foreach ($values as $value => $objects) {
                // Если фильтр среди таблицы рейсов - указываем что нужно выполнить ее join
                if(in_array($key, $this->flightsColumns)) {
                    $this->joinFlights = true;
                }
                // Если фильтр среди таблицы ответственных подразделений, и при этом это не null
                // (нужно по той причине, что при джоине запрос отсутствующих ответственных
                // подразделений выдает пустой список событий, даже когда такие события есть)
                if(in_array($key, $this->responsibleDepartmentsColumns) && filled($value)) {
                    // Указываем что нужно выполнить join таблицы ответственных подразделений
                    $this->joinResponsibleDepartments = true;
                }
                // Если это одиночный фильтр (не массив) - указываем его как значение
                if(in_array($key, EventFilter::SINGLE_FILTERS)) {
                    $result[$key] = $value;
                } else {
                    // Иначе - добавляем его значение в массив значений
                    $result[$key][] = $value;
                }
            }
        }
        // Возвращаем результат
        return $result;
    }

    /**
     * Форматирует фильтры для текущего пользователя.
     *
     * @return array массив с отформатированными фильтрами.
     */
    public function formatFilters()
    {
        // Заводим фильтры
        $filters = $this->filters;
        // Перебираем все возможные фильтры
        foreach (EventFilter::FILTERS as $filter) {
            // Если в массиве фильтров нет указанного фильтра
            if(!array_key_exists($filter, $filters)) {
                // Заполняем его либо пустым значением, либо пустым массивом (в зависимости от того одиночный фильтр или нет)
                $filters[$filter] = in_array($filter, EventFilter::SINGLE_FILTERS) ? "" : [];
            }
        }
        // Возвращаем отформатированные фильтры
        return $filters;
    }
}
