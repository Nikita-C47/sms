<?php

namespace App\Http\Controllers;

use App\Components\Helpers\EventFiltersHelper;
use App\Events\EventProcessed;
use App\Events\RDsAdded;
use App\Events\RDsRemoved;
use App\Http\Requests\EventFiltersFormRequest;
use App\Http\Requests\Events\AnonymousEventFormRequest;
use App\Http\Requests\Events\EventCategoriesFormRequest;
use App\Http\Requests\Events\FindEventFormRequest;
use App\Http\Requests\Events\FlightsFormRequest;
use App\Http\Requests\Events\EventFormRequest;
use App\Http\Requests\Events\ProcessEventFormRequest;
use App\Models\Department;
use App\Models\Events\Event;
use App\Models\Events\EventAttachment;
use App\Models\Events\EventCategory;
use App\Models\Events\EventFilter;
use App\Models\Events\EventMeasure;
use App\Models\Events\EventRelation;
use App\Models\Events\EventResponsibleDepartment;
use App\Models\Events\EventType;
use App\Models\Flight;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /** @var \App\User $user */
        $user = Auth::user();

       // DB::enableQueryLog();
        $events = Event::approved()->with([
            'flight',
            'relation',
            'department',
            'user_created_by',
            'responsible_departments'
        ])->select(['events.*']);

        $events = $this->loadFilters($events);

        if($user->access_level === 'user') {
            $events = $events->where('events.created_by', $user->id);
        }

        $events = $events->orderBy('events.updated_at', 'desc')->paginate(10);
        //dd($events);
        //dd(DB::getQueryLog());
        return view('events.index', [
            'events' => $events
        ]);
    }

    public function indexNeedsApproval()
    {
        $events = Event::needsApproval()->paginate(10);
        return view('events.anonymous.index', [
            'events' => $events
        ]);
    }

    public function indexNotApproved()
    {
        // TODO: Сделать переход по номеру события на страницу редактирования, чтобы его можно было одобрить там
        $events = Event::notApproved()->paginate(10);
        return view('events.anonymous.rejected', [
            'events' => $events
        ]);
    }

    public function indexTrashed()
    {
        $events = Event::onlyTrashed()->with([
            'flight',
            'relation',
            'department',
            'user_created_by',
            'responsible_departments'
        ])->paginate(10);
        return view('events.trashed', [
            'events' => $events
        ]);
    }

    public function search()
    {
        return view('events.search.form');
    }

    public function find(FindEventFormRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $id = $request->get('query');

        $relations = [
            'flight',
            'relation',
            'department',
            'user_created_by',
            'responsible_departments'
        ];

        if($user->access_level === 'admin') {
            $event = Event::withTrashed()->with($relations)->find($id);
        } else {
            $event = Event::with($relations);
            if($user->access_level === 'user') {
                $event = $event->where([
                    ['id', $id],
                    ['created_by', $user->id]
                ])->first();
            } else {
                $event = $event->find($id);
            }
        }

        return view('events.search.results', [
            'id' => $id,
            'event' => $event
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $relations = EventRelation::all();
        $departments = Department::all();
        $types = EventType::all();

        return view('events.create', [
            'statuses' => Event::EVENT_STATUSES,
            'relations' => $relations,
            'departments' => $departments,
            'types' => $types
        ]);
    }

    public function createAnonymous()
    {
        return view('events.anonymous.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EventFormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventFormRequest $request)
    {
        /** @var Event $event */
        $event = DB::transaction(function () use ($request) {
            $date = Carbon::createFromFormat('d.m.Y', $request->get('date'));

            $event = new Event([
                'date' => $date->toDateTimeString(),
                'flight_id' => $request->get('flight_id'),
                'department_id' => $request->get('department_id'),
                'relation_id' => $request->get('relation_id'),
                'type_id' => $request->get('type_id'),
                'category_id' => $request->get('category_id'),
                'approved' => true,
                'initiator' => $request->get('initiator'),
                'airport' => $request->get('airport'),
                'status' => $request->get('status'),
                'message' => $request->get('message'),
                'commentary' => $request->get('commentary'),
                'created_by' => Auth::user()->getAuthIdentifier(),
                'updated_by' => Auth::user()->getAuthIdentifier(),
            ]);

            $event->save();

            if($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $attachment = new EventAttachment([
                        'event_id' => $event->id,
                        'original_name' => $file->getClientOriginalName(),
                        'extension' => $file->getClientOriginalExtension(),
                        'size' => $file->getSize(),
                        'created_by' => Auth::user()->getAuthIdentifier()
                    ]);
                    $attachment->saveInFilesystem($file);
                    $attachment->save();
                }
            }

            return $event;
        });

        $request->session()->flash('alert', [
            'type' => 'success',
            'text' => 'Новое событие #'.$event->id.' успешно добавлено'
        ]);

        return response('OK', 200);
    }

    public function storeAnonymous(AnonymousEventFormRequest $request)
    {
        DB::transaction(function () use ($request) {
            $date = Carbon::createFromFormat('d.m.Y', $request->get('date'));

            $event = new Event([
                'date' => $date->format('Y-m-d H:i:s'),
                'initiator' => $request->get('initiator'),
                'airport' => $request->get('airport'),
                'message' => $request->get('message'),
                'commentary' => $request->get('commentary')
            ]);

            $event->save();
        });

        return view('events.anonymous.success');
    }

    public function processEvent(ProcessEventFormRequest $request, $id)
    {
        /** @var Event $event */
        $event = DB::transaction(function () use ($request, $id) {
            /** @var Event $event */
            $event = Event::sharedLock()->findOrFail($id);
            $event->approved = $request->get('approved');
            $event->updated_by = Auth::user()->getAuthIdentifier();
            $event->notify = false;
            $event->save();

            return $event;
        });

        event(new EventProcessed($event, Auth::user()));

        $action = $event->approved ? 'одобрено' : 'отклонено';

        return redirect()->route('events-needs-approval')->with('alert', [
            'type' => 'success',
            'text' => 'Событие было успешно ' . $action
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::withTrashed()->with([
            'flight',
            'relation',
            'department',
            'type',
            'category',
            'user_created_by',
            'user_updated_by',
            'measures',
            'responsible_departments',
            'attachments'
        ])->findOrFail($id);

        Gate::authorize('view-event', $event);

        return view('events.view', [
            'event' => $event
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $event = Event::with([
            'flight',
            'relation',
            'department',
            'type',
            'category',
            'user_created_by',
            'user_updated_by',
            'measures.user_created_by',
            'responsible_departments',
            'attachments.user_created_by'
        ])->findOrFail($id);

        $relations = EventRelation::all();
        $departments = Department::all();
        $types = EventType::all();

        return view('events.edit', [
            'event' => $event,
            'statuses' => Event::EVENT_STATUSES,
            'relations' => $relations,
            'departments' => $departments,
            'types' => $types
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EventFormRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EventFormRequest $request, $id)
    {
        // Получаем указанное событие
        /** @var Event $event */
        $event = Event::with('responsible_departments')->findOrFail($id);
        // Собираем существующие и новые ответственные подразделения
        $existingRDs = collect($event->responsible_departments->modelKeys());
        $newRDs = collect($request->get('responsible_departments'));
        // Запускаем транзакцию
        $event = DB::transaction(function () use ($request, $event) {

            // 0. Подготавливаем данные

            // Формируем дату
            $date = Carbon::createFromFormat('d.m.Y', $request->get('date'));
            // Дата устранения
            $fixDate = null;
            // Если дата устранения указана - заполняем ее
            if(filled($request->get('fix_date'))) {
                $fixDate = Carbon::createFromFormat('d.m.Y', $request->get('fix_date'))->toDateTimeString();
            }

            // 1. Обновляем событие

            // Блокируем запись для обновления
            $event->sharedLock();
            // Заполняем событие новыми данными
            /** @var Event $event */
            $event->fill([
                'date' => $date->toDateTimeString(),
                'flight_id' => $request->get('flight_id'),
                'department_id' => $request->get('department_id'),
                'relation_id' => $request->get('relation_id'),
                'type_id' => $request->get('type_id'),
                'category_id' => $request->get('category_id'),
                'approved' => $request->get('approved'),
                'initiator' => $request->get('initiator'),
                'airport' => $request->get('airport'),
                'status' => $request->get('status'),
                'message' => $request->get('message'),
                'commentary' => $request->get('commentary'),
                'reason' => $request->get('reason'),
                'decision' => $request->get('decision'),
                'fix_date' => $fixDate,
                'updated_by' => Auth::user()->getAuthIdentifier(),
            ]);
            // Сохраняем событие
            $event->save();

            // 2. Обновляем связные данные

            // Удаляем существующие ответственные подразделения
            EventResponsibleDepartment::sharedLock()->where('event_id', $event->id)->delete();
            // Если есть ответственные подразделения
            if(filled($request->get('responsible_departments'))) {
                // Перебираем их
                foreach ($request->get('responsible_departments') as $departmentId) {
                    // Добавляем новое подразделение
                    $newResponsibleDepartment = new EventResponsibleDepartment([
                        'event_id' => $event->id,
                        'department_id' => $departmentId
                    ]);
                    $newResponsibleDepartment->save();
                }
            }

            // Перебираем добавленные мероприятия
            for($i = 0; $i < $request->get('measures_count'); $i++) {
                // Добавляем новое мероприятие
                $newMeasure = new EventMeasure([
                    'event_id' => $event->id,
                    'text' => $request->get('measure_'.$i),
                    'created_by' => Auth::user()->getAuthIdentifier()
                ]);
                $newMeasure->save();
            }

            // Если есть прикрепленные файлы
            if($request->hasFile('attachments')) {
                // Перебираем их
                foreach ($request->file('attachments') as $file) {
                    // Добавляем файл в БД
                    $attachment = new EventAttachment([
                        'event_id' => $event->id,
                        'original_name' => $file->getClientOriginalName(),
                        'extension' => $file->getClientOriginalExtension(),
                        'size' => $file->getSize(),
                        'created_by' => Auth::user()->getAuthIdentifier()
                    ]);
                    // Сохраняем его в файловой системе
                    $attachment->saveInFilesystem($file);
                    $attachment->save();
                }
            }

            // 3. Удаляем существующие данные

            // Получаем ключи мероприятий, которые нужно удалить
            $removalArray = $this->getMultipleKeys($request, 'removed_measure_', 'removed_measures_count');
            // Получаем указанные мероприятия
            $itemsToRemove = EventMeasure::sharedLock()->find($removalArray);
            // Перебираем их
            /** @var EventMeasure $measure */
            foreach ($itemsToRemove as $measure) {
                // Удаляем мероприятие
                $measure->delete();
            }
            // Получаем ключи вложений, которые нужно удалить
            $removalArray = $this->getMultipleKeys($request, 'removed_attachment_', 'removed_attachments_count');
            // Получаем указанные вложения
            $itemsToRemove = EventAttachment::sharedLock()->find($removalArray);
            // Перебираем их
            /** @var EventAttachment $attachment */
            foreach ($itemsToRemove as $attachment) {
                // Удаляем вложение
                $attachment->delete();
            }

            // Возвращаем обновленное событие
            return $event;
        });

        // 4. Генерируем события

        // Событие, инициирующее отправку уведомлений пользователям новых ответственных подразделений
        event(new RDsAdded($newRDs->diff($existingRDs)->toArray(), $event, Auth::user()));
        // Событие, инициирующее отправку уведомлений пользователям удаленных ответственных подразделений
        event(new RDsRemoved($existingRDs->diff($newRDs)->toArray(), $event, Auth::user()));

        // Добавляем флэш-уведомление в сессию
        $request->session()->flash('alert', [
            'type' => 'success',
            'text' => 'Событие №'.$id.' успешно обновлено'
        ]);

        // Возвращаем успешный ответ
        return response('OK', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /** @var Event $event */
        $event = DB::transaction(function () use ($id) {
            /** @var Event $event */
            $event = Event::sharedLock()->findOrFail($id);
            $event->updated_by = Auth::user()->getAuthIdentifier();
            $event->deleted_by = Auth::user()->getAuthIdentifier();
            $event->notify = false;
            $event->save();
            $event->delete();

            return $event;
        });

        return redirect()->route('home')->with('alert', [
            'type' => 'success',
            'text' => 'Событие №'.$event->id.' успешно удалено'
        ]);
    }

    public function restore($id)
    {
        /** @var Event $event */
        $event = DB::transaction(function () use ($id) {
            /** @var Event $event */
            $event = Event::onlyTrashed()->sharedLock()->findOrFail($id);
            $event->updated_by = Auth::user()->getAuthIdentifier();
            $event->notify = false;
            //$event->deleted_by = null;
            $event->save();
            $event->restore();

            return $event;
        });

        return redirect()->route('home')->with('alert', [
            'type' => 'success',
            'text' => 'Событие №'.$event->id.' успешно восстановлено'
        ]);
    }

    public function forceDelete($id)
    {
        /** @var Event $event */
        $event = DB::transaction(function () use ($id) {
            /** @var Event $event */
            $event = Event::onlyTrashed()->sharedLock()->findOrFail($id);
            $event->notify = false;
            $event->forceDelete();
            return $event;
        });

        Storage::disk('public')->deleteDirectory('events/'.$event->id);

        return redirect()->route('home')->with('alert', [
            'type' => 'success',
            'text' => 'Событие №'.$event->id.' безвозвратно удалено из базы данных'
        ]);
    }

    public function getEventCategories(EventCategoriesFormRequest $request)
    {
        $departmentId = $request->get('department_id');
        $categories = EventCategory::where('department_id', $departmentId)->get();

        return response()->json(['categories' => $categories]);
    }

    public function getFlights(FlightsFormRequest $request)
    {
        $date = $request->get('date');
        $flights = Flight::whereDate('departure_datetime', $date)->get();

        return response()->json(['flights' => $flights]);
    }

    // TODO: Добавить кэширование фильтров
    public function filters()
    {
        $helper = new EventFiltersHelper();
        $boards = Flight::distinct('board')->get();
        $captains = Flight::distinct('captain')->get();
        $airports = Event::whereNotNull('airport')->distinct('airport')->get();
        // RESPONSIBLE DEPARTMENTS!!!!
        $departments = EventResponsibleDepartment::with('department')->distinct('department_id')->get();
        $users = Event::with('user_created_by')->distinct('created_by')->get();
        $relations = Event::with('relation')->whereNotNull('relation_id')->distinct('relation_id')->get();

        return view('events.filters', [
            'boards' => $boards,
            'captains' => $captains,
            'airports' => $airports,
            'statuses' => Event::EVENT_STATUSES,
            'departments' => $departments,
            'users' => $users,
            'relations' => $relations,
            'filters' => $helper->formatFilters()
        ]);
    }

    public function setFilters(EventFiltersFormRequest $request)
    {
        // Запускаем транзакцию
        DB::transaction(function () use ($request) {
            // Удаляем текущие фильтры
            EventFilter::where('user_id', Auth::user()->getAuthIdentifier())->delete();
            // Сохраняем начальную дату
            $this->saveDateFilter($request, 'date_from');
            // Сохраняем конечную дату
            $this->saveDateFilter($request, 'date_to');
            // Сохраняем борты
            $this->saveFilter($request, 'boards');
            // Сохраняем КВС
            $this->saveFilter($request, 'captains');
            // Сохраняем "Где произошло"
            $this->saveFilter($request, 'airports');
            // Сохраняем статусы
            $this->saveFilter($request, 'statuses');
            // Сохраняем ответственные подразделения
            $this->saveFilter($request, 'responsible_departments');
            // Сохраняем пользователей
            $this->saveFilter($request, 'users');
            // Сохраняем к чему относится
            $this->saveFilter($request, 'relations');
            // Сохраняем фильтр по прикрепленным файлам
            if($request->has('attachments') && filled($request->get('attachments'))) {
                $filter = new EventFilter([
                    'user_id' => Auth::user()->getAuthIdentifier(),
                    'key' => 'attachments',
                    'value' => $request->get('attachments')
                ]);
                $filter->save();
            }
        });

        return redirect()->route('home')->with('alert', [
            'type' => 'success',
            'text' => 'Фильтры были успешно обновлены'
        ]);

    }

    private function loadFilters(Builder $query)
    {
        $helper = new EventFiltersHelper();
        $filters = $helper->filters;

        if($helper->joinFlights) {
            $query = $query->join(
                'flights',
                'events.flight_id',
                '=',
                'flights.id'
            );
        }

        if($helper->joinResponsibleDepartments) {
            $query = $query->join(
                'event_responsible_departments',
                'events.id',
                '=',
                'event_responsible_departments.event_id'
            );
        }

        foreach ($filters as $column => $filter) {
            $query = $this->loadFilter($query, $column, $filter);
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param string $column
     * @param $filter
     * @return Builder|\Illuminate\Database\Query\Builder
     * @throws \Exception
     */
    private function loadFilter(Builder $query, string $column, $filter)
    {
        switch ($column) {
            case "date_from": {
                return $query->whereDate('date', '>=', $filter);
                break;
            }
            case "date_to": {
                return $query->whereDate('date', '<=', $filter);
                break;
            }
            case "boards": {
                $query = $this->getFilter($query, 'flights.board', $filter);
                break;
            }
            case "captains": {
                $query = $this->getFilter($query, 'flights.captain', $filter);
                break;
            }
            case "airports": {
                $query = $this->getFilter($query, 'events.airport', $filter);
                break;
            }
            case "statuses": {
                $query = $this->getFilter($query, 'events.status', $filter);
                break;
            }
            case "responsible_departments": {
                if(filled($filter[0])) {
                    $query = $query->whereIn('event_responsible_departments.department_id', $filter);
                } else {
                    $query = $query->doesntHave('responsible_departments');
                }
                break;
            }
            case "users": {
                $query = $this->getFilter($query, 'events.created_by', $filter);
                break;
            }
            case "relations": {
                $query = $this->getFilter($query, 'events.relation_id', $filter);
                break;
            }
            case "attachments": {
                if($filter === 1) {
                    $query = $query->has('attachments');
                } else {
                    $query = $query->doesntHave('attachments');
                }
                break;
            }
            default: {
                throw new \Exception("Unknown database filter");
            }
        }
        return $query;
    }

    private function getFilter(Builder $query, string $dbColumn, $filter)
    {
        if(filled($filter[0])) {
            $query = $query->whereIn($dbColumn, $filter);
        } else {
            $query = $query->whereNull($dbColumn);
        }
        return $query;
    }

    private function saveFilter(EventFiltersFormRequest $request, string $fieldName)
    {
        // Если отмечен чекбокс "все", не нужно сохранять отдельный фильтр
        if($request->has($fieldName . '-all')) {
            return;
        }

        if(!$request->has($fieldName)) {
            $filter = new EventFilter([
                'user_id' => Auth::user()->getAuthIdentifier(),
                'key' => $fieldName
            ]);
            $filter->save();

        } else {
            foreach ($request->get($fieldName) as $value) {
                $filter = new EventFilter([
                    'user_id' => Auth::user()->getAuthIdentifier(),
                    'key' => $fieldName,
                    'value' => $value
                ]);
                $filter->save();
            }
        }
    }

    private function saveDateFilter(EventFiltersFormRequest $request, string $fieldName)
    {
        // Если выбрана дата
        if(filled($request->get($fieldName))) {
            // Инициализируем дату
            $date = Carbon::createFromFormat('d.m.Y', $request->get($fieldName));
            // Добавляем фильтр
            $filter = new EventFilter([
                'user_id' => Auth::user()->getAuthIdentifier(),
                'key' => $fieldName,
                'value' => $date->startOfDay()->toDateTimeString()
            ]);
            $filter->save();
        }
    }

    /**
     * Извлекает ключи у группированных полей (например у списка мероприятий, которые надо удалить)
     *
     * @param FormRequest $request Объект запроса
     * @param string $prefix Префикс для группированных полей
     * @param string $count Название поля, содержащего счетчик элементов
     * @return array массив со списком значений указанных полей
     */
    private function getMultipleKeys(FormRequest $request, string $prefix, string $count)
    {
        // Тут будет результат
        $result = [];
        // Перебираем поля с указанным префиксом
        for($i = 0; $i < $request->get($count); $i++) {
            // Заполняем массив
            $result[] = $request->get($prefix.$i);
        }
        // Возвращаем результат
        return $result;
    }
}
