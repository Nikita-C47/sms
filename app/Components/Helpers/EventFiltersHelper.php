<?php

namespace App\Components\Helpers;

use App\Models\Events\EventFilter;
use Illuminate\Support\Facades\Auth;

class EventFiltersHelper
{
    protected $flightsColumns = [
        'boards',
        'captains',
    ];

    protected $responsibleDepartmentsColumns = [
        'responsible_departments'
    ];

    public $filters = [];

    public $joinFlights = false;

    public $joinResponsibleDepartments = false;

    public function __construct()
    {
        $this->filters = $this->getFilters();
    }

    public function getFilters()
    {
        /** @var \App\User $user */
        $user = Auth::user();
        $filters = $user->event_filters->groupBy(['key', 'value'])->toArray();

        $result = [];

        foreach ($filters as $key => $values) {
            foreach ($values as $value => $objects) {

                if(in_array($key, $this->flightsColumns)) {
                    $this->joinFlights = true;
                }
                // Фикс для ответственных подразделений (точнее их отсутствия)
                if(in_array($key, $this->responsibleDepartmentsColumns) && filled($value)) {
                    $this->joinResponsibleDepartments = true;
                }

                if(in_array($key, EventFilter::SINGLE_FILTERS)) {
                    $result[$key] = $value;
                } else {
                    $result[$key][] = $value;
                }
            }
        }

        return $result;
    }

    public function formatFilters()
    {
        $filters = $this->filters;

        foreach (EventFilter::FILTERS as $filter) {
            if(!array_key_exists($filter, $filters)) {
                $filters[$filter] = in_array($filter, EventFilter::SINGLE_FILTERS) ? "" : [];
            }
        }

        return $filters;
    }
}
