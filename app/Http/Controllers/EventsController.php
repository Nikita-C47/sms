<?php

namespace App\Http\Controllers;

use App\Components\Helpers\EventFiltersHelper;
use App\Events\{EventProcessed, RDsAdded, RDsRemoved};
use App\Http\Requests\{EventFiltersFormRequest,
    Events\AnonymousEventFormRequest,
    Events\EventCategoriesFormRequest,
    Events\FindEventFormRequest,
    Events\FlightsFormRequest,
    Events\EventFormRequest,
    Events\ProcessEventFormRequest};
use App\Models\{Department,
    Events\Event,
    Events\EventAttachment,
    Events\EventCategory,
    Events\EventFilter,
    Events\EventMeasure,
    Events\EventRelation,
    Events\EventResponsibleDepartment,
    Events\EventType,
    Flight};
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\{Auth, DB, Gate, Storage};

/**
 * Класс, представляющий контроллер событий.
 * @package App\Http\Controllers Контроллеры приложения.
 */
class EventsController extends Controller
{
    /**
     * Отображает список событий.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Получаем текущего пользователя
        /** @var \App\User $user */
        $user = Auth::user();
        // начинаем формировать запрос на события (показываем только одобренные)
        $events = Event::approved()->with([
            'flight',
            'relation',
            'department',
            'user_created_by',
            'responsible_departments'
        ])->select(['events.*']);
        // Загружаем фильтры для списка событий
        $events = $this->loadFilters($events);
        // Если это пользователь
        if($user->access_level === 'user') {
            // Он может видеть только свои события
            $events = $events->where('events.created_by', $user->id);
        }
        // Добавляем сортировку и пагинацию
        $events = $events->orderBy('events.updated_at', 'desc')->paginate(10);
        // Возвращаем представление
        return view('events.index', [
            'events' => $events
        ]);
    }

    /**
     * Отоброажает список событий, требующих одобрения.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexNeedsApproval()
    {
        // Загружаем события, требующие уведомления и отображаем их
        $events = Event::needsApproval()->paginate(10);
        return view('events.anonymous.index', [
            'events' => $events
        ]);
    }

    /**
     * Отоброажает список отклоненных событий.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexNotApproved()
    {
        // Загружаем отклоненные события и отображаем их
        $events = Event::notApproved()->paginate(10);
        return view('events.anonymous.rejected', [
            'events' => $events
        ]);
    }

    /**
     * Отоброажает список удаленных событий.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexTrashed()
    {
        // Запрашиваем удаленные события
        $events = Event::onlyTrashed()->with([
            'flight',
            'relation',
            'department',
            'user_created_by',
            'responsible_departments'
        ])->paginate(10);
        // Возвращаем представление
        return view('events.trashed', [
            'events' => $events
        ]);
    }

    /**
     * Отображает страницу с формой поиска события (отдельную).
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search()
    {
        return view('events.search.form');
    }

    /**
     * Осуществляет поиск события по его номеру.
     *
     * @param FindEventFormRequest $request запрос на поиск события по номеру.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function find(FindEventFormRequest $request)
    {
        // Получаем текущего пользователя
        /** @var User $user */
        $user = Auth::user();
        // Получаем номер события
        $id = $request->get('query');
        // Список загружаемых отношений
        $relations = [
            'flight',
            'relation',
            'department',
            'user_created_by',
            'responsible_departments'
        ];
        // Если это администратор
        if($user->access_level === 'admin') {
            // Ищем среди всех событий, в том числе и удаленных
            $event = Event::withTrashed()->with($relations)->find($id);
        } else {
            // Иначе загружаем отношения
            $event = Event::with($relations);
            // Если это пользователь
            if($user->access_level === 'user') {
                // Ищем только среди его событий
                $event = $event->where([
                    ['id', $id],
                    ['created_by', $user->id]
                ])->first();
            } else {
                // Если это менеджер событий - ищем среди всех
                $event = $event->find($id);
            }
        }
        // Возвращаем представление с результатами поиска
        return view('events.search.results', [
            'id' => $id,
            'event' => $event
        ]);
    }

    /**
     * Отображает форму создания события.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Получаем нужные справочники
        $relations = EventRelation::all();
        $departments = Department::all();
        $types = EventType::all();
        // Отображаем представление
        return view('events.create', [
            'statuses' => Event::EVENT_STATUSES,
            'relations' => $relations,
            'departments' => $departments,
            'types' => $types
        ]);
    }

    /**
     * Отображает форму создания анонимного события.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createAnonymous()
    {
        return view('events.anonymous.create');
    }

    /**
     * Сохраняет событие в БД.
     *
     * @param EventFormRequest $request запрос на добавление события.
     * @return \Illuminate\Http\Response
     */
    public function store(EventFormRequest $request)
    {
        // Запускаем транзакцию
        /** @var Event $event */
        $event = DB::transaction(function () use ($request) {
            // Генерируем дату события
            $date = Carbon::createFromFormat('d.m.Y', $request->get('date'));
            // Создаем событие
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
            // Если есть вложения
            if($request->hasFile('attachments')) {
                // Перебираем их
                foreach ($request->file('attachments') as $file) {
                    // Добавляем вложение
                    $attachment = new EventAttachment([
                        'event_id' => $event->id,
                        'original_name' => $file->getClientOriginalName(),
                        'extension' => $file->getClientOriginalExtension(),
                        'size' => $file->getSize(),
                        'created_by' => Auth::user()->getAuthIdentifier()
                    ]);
                    // Сохраняем в файловой системе
                    $attachment->saveInFilesystem($file);
                    // Сохраняем событие (именно в таком порядке, так как предыдущий метод проставляет имя файла в модели)
                    $attachment->save();
                }
            }
            // Возвращаем событие
            return $event;
        });
        // Добавляем в сессию уведомление
        $request->session()->flash('alert', [
            'type' => 'success',
            'text' => 'Новое событие #'.$event->id.' успешно добавлено'
        ]);
        // Возвращаем ответ
        return response('OK', 200);
    }

    /**
     * Сохраняет анонимное событие в БД.
     *
     * @param AnonymousEventFormRequest $request запрос на добавление анонимного события.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function storeAnonymous(AnonymousEventFormRequest $request)
    {
        // Запускаем транзакцию
        DB::transaction(function () use ($request) {
            // Генерируем дату события
            $date = Carbon::createFromFormat('d.m.Y', $request->get('date'));
            // Сохраняем событие
            $event = new Event([
                'date' => $date->format('Y-m-d H:i:s'),
                'initiator' => $request->get('initiator'),
                'airport' => $request->get('airport'),
                'message' => $request->get('message'),
                'commentary' => $request->get('commentary')
            ]);
            $event->save();
        });
        // Отображаем t.y.p
        return view('events.anonymous.success');
    }

    /**
     * Обрабатывает событие.
     *
     * @param ProcessEventFormRequest $request запрос на обработку события.
     * @param int $id ID события.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processEvent(ProcessEventFormRequest $request, $id)
    {
        // Запускаем транзакцию
        /** @var Event $event */
        $event = DB::transaction(function () use ($request, $id) {
            // Ищем событие в БД и блокируем его
            /** @var Event $event */
            $event = Event::sharedLock()->findOrFail($id);
            // Устанавливаем статус одобрения
            $event->approved = $request->get('approved');
            // Указываем что событие было обновлено пользователем
            $event->updated_by = Auth::user()->getAuthIdentifier();
            // Не отправляем уведомление (иначе уйдет уведомление об обновлении события)
            $event->notify = false;
            // Сохраняем событие
            $event->save();
            // Возвращаем событие
            return $event;
        });
        // Генерируем событие
        event(new EventProcessed($event, Auth::user()));
        // Устанавливаем статус события для уведомления
        $action = $event->approved ? 'одобрено' : 'отклонено';
        // Делаем редирект на список не обработанных событий с уведомлением
        return redirect()->route('events-needs-approval')->with('alert', [
            'type' => 'success',
            'text' => 'Событие было успешно ' . $action
        ]);
    }

    /**
     * Отображает информацию о событии.
     *
     * @param int $id ID события.
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Ищем событие среди всех
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
        // Проверяем может ли пользователь просматривать событие
        Gate::authorize('view-event', $event);
        // Возвращаем представление
        return view('events.view', [
            'event' => $event
        ]);
    }

    /**
     * Отображает форму редактирования события.
     *
     * @param int $id ID события.
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Ищем событие
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
        // Загружаем нужные справочники
        $relations = EventRelation::all();
        $departments = Department::all();
        $types = EventType::all();
        // Отображаем представление
        return view('events.edit', [
            'event' => $event,
            'statuses' => Event::EVENT_STATUSES,
            'relations' => $relations,
            'departments' => $departments,
            'types' => $types
        ]);
    }

    /**
     * Обновляет событие в БД.
     *
     * @param EventFormRequest $request запрос на обновление события.
     * @param int $id ID события.
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
     * Удаляет событие.
     *
     * @param int $id ID события.
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Запускаем транзакцию
        /** @var Event $event */
        $event = DB::transaction(function () use ($id) {
            // Ищем событие и блокируем его
            /** @var Event $event */
            $event = Event::sharedLock()->findOrFail($id);
            // Устанавливаем обновившего и удалившего
            $event->updated_by = Auth::user()->getAuthIdentifier();
            $event->deleted_by = Auth::user()->getAuthIdentifier();
            // Предотавращаем отправку уведомлений об обновлении события
            $event->notify = false;
            // Сохраняем событие
            $event->save();
            // Удаляем событие
            $event->delete();
            // Возвращаем событие
            return $event;
        });
        // Возвращаем редирект на список событий с уведомлением
        return redirect()->route('home')->with('alert', [
            'type' => 'success',
            'text' => 'Событие №'.$event->id.' успешно удалено'
        ]);
    }

    /**
     * Восстанавливает событие в БД.
     *
     * @param int $id ID события.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        // Запускаем транзакцию
        /** @var Event $event */
        $event = DB::transaction(function () use ($id) {
            /** @var Event $event */
            $event = Event::onlyTrashed()->sharedLock()->findOrFail($id);
            // Устанавливаем обновившего событие
            $event->updated_by = Auth::user()->getAuthIdentifier();
            // Предотвращаем отправку уведомлений об обновлении события
            $event->notify = false;
            // Сохраняем событие
            $event->save();
            // Восстанавливаем его
            $event->restore();
            // Возвращаем событие
            return $event;
        });
        // Возвращаем редирект на список событий с уведомлением
        return redirect()->route('home')->with('alert', [
            'type' => 'success',
            'text' => 'Событие №'.$event->id.' успешно восстановлено'
        ]);
    }

    /**
     * Уничтожает событие.
     *
     * @param int $id ID события.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete($id)
    {
        // Запускаем транзакцию
        /** @var Event $event */
        $event = DB::transaction(function () use ($id) {
            // Ищем событие и блокируем его
            /** @var Event $event */
            $event = Event::onlyTrashed()->sharedLock()->findOrFail($id);
            // Уничтожаем событие
            $event->forceDelete();
            // Возвращаем событие
            return $event;
        });
        // Удаляем все файлы, связанные с событием
        Storage::disk('public')->deleteDirectory('events/'.$event->id);
        // Возвращаем редирект на список событий с уведомлением
        return redirect()->route('home')->with('alert', [
            'type' => 'success',
            'text' => 'Событие №'.$event->id.' безвозвратно удалено из базы данных'
        ]);
    }

    /**
     * Возвращает список категорий по переданному ID подразделения.
     *
     * @param EventCategoriesFormRequest $request запрос на получение списка категорий.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEventCategories(EventCategoriesFormRequest $request)
    {
        // Получаем ID подразделения
        $departmentId = $request->get('department_id');
        // Получаем категории
        $categories = EventCategory::where('department_id', $departmentId)->get();
        // Возвращаем JSON
        return response()->json(['categories' => $categories]);
    }

    /**
     * Возвращает список рейсов на указанную дату.
     *
     * @param FlightsFormRequest $request запрос на получение рейсов.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFlights(FlightsFormRequest $request)
    {
        // Получаем дату
        $date = $request->get('date');
        // Ищем рейсы на указанную дату
        $flights = Flight::whereDate('departure_datetime', $date)->get();
        // Возвращаем JSON
        return response()->json(['flights' => $flights]);
    }

    /**
     * Отображает форму редактирования фильтров списка событий.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function filters()
    {
        // Заводим экземпляр помощника по фильтрам
        $helper = new EventFiltersHelper();
        // Получаем нужные справочники

        // Борта
        $boards = Flight::distinct('board')->get();
        // КВС
        $captains = Flight::distinct('captain')->get();
        // Где произошло
        $airports = Event::whereNotNull('airport')->distinct('airport')->get();
        // Подразделения (только те, что есть в ответственных)
        $departments = EventResponsibleDepartment::with('department')->distinct('department_id')->get();
        // Пользователи (только те, что создавали события)
        $users = Event::with('user_created_by')->distinct('created_by')->get();
        // Мероприятия к которым относится (только те, что есть в событиях)
        $relations = Event::with('relation')->whereNotNull('relation_id')->distinct('relation_id')->get();
        // Возвращаем представление
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

    /**
     * Сохраняет фильтры.
     *
     * @param EventFiltersFormRequest $request запрос на сохранение фильтров.
     * @return \Illuminate\Http\RedirectResponse
     */
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
        // Возвращаем редирект на список событий с уведомлением
        return redirect()->route('home')->with('alert', [
            'type' => 'success',
            'text' => 'Фильтры были успешно обновлены'
        ]);

    }

    /**
     * Функция-помощник для загрузки фильтров списка событий.
     *
     * @param Builder $query текущий объект текущий объект построителя запросов.
     * @return Builder|\Illuminate\Database\Query\Builder измененный объект построителя запросов.
     * @throws \Exception исключение при запросе неизвестного фильтра.
     */
    private function loadFilters(Builder $query)
    {
        // Заводим хелпер для работы с фильтрами
        $helper = new EventFiltersHelper();
        // Получаем фильтры
        $filters = $helper->filters;
        // Если нужно - делаем join таблицы с рейсами
        if($helper->joinFlights) {
            $query = $query->join(
                'flights',
                'events.flight_id',
                '=',
                'flights.id'
            );
        }
        // Если нужно - делаем join таблицы ответственных подразделений
        if($helper->joinResponsibleDepartments) {
            $query = $query->join(
                'event_responsible_departments',
                'events.id',
                '=',
                'event_responsible_departments.event_id'
            );
        }
        // Перебираем фильтры
        foreach ($filters as $column => $filter) {
            // Загружаем фильтр
            $query = $this->loadFilter($query, $column, $filter);
        }
        // Возвращаем обновленный объект запроса
        return $query;
    }

    /**
     * Функция-помощник для загрузки фильтра.
     *
     * @param Builder $query текущий объект построителя запросов.
     * @param string $column тип фильтра.
     * @param mixed $filter значение фильтра.
     * @return Builder|\Illuminate\Database\Query\Builder объект построителя запросов с примененным фильтром.
     * @throws \Exception исключение при запросе неизвестного фильтра.
     */
    private function loadFilter(Builder $query, string $column, $filter)
    {
        // Проверяем тип фильтра
        switch ($column) {
            // Начальная дата
            case "date_from": {
                return $query->whereDate('date', '>=', $filter);
                break;
            }
            // Конечная дата
            case "date_to": {
                return $query->whereDate('date', '<=', $filter);
                break;
            }
            // Борты
            case "boards": {
                $query = $this->getFilter($query, 'flights.board', $filter);
                break;
            }
            // КВС
            case "captains": {
                $query = $this->getFilter($query, 'flights.captain', $filter);
                break;
            }
            // Где произошло
            case "airports": {
                $query = $this->getFilter($query, 'events.airport', $filter);
                break;
            }
            // Статусы
            case "statuses": {
                $query = $this->getFilter($query, 'events.status', $filter);
                break;
            }
            // Ответственные подразделения
            case "responsible_departments": {
                // Если фильтр не по типу "Пустое значение" - фильтруем, иначе - ищем где null
                $query = filled($filter[0]) ? $query->whereIn('event_responsible_departments.department_id', $filter) : $query->doesntHave('responsible_departments');
                break;
            }
            // Пользователи, создавшие событие
            case "users": {
                $query = $this->getFilter($query, 'events.created_by', $filter);
                break;
            }
            // К чему относится событие
            case "relations": {
                $query = $this->getFilter($query, 'events.relation_id', $filter);
                break;
            }
            // Вложения
            case "attachments": {
                $query = $filter ? $query->has('attachments') : $query->doesntHave('attachments');
                break;
            }
            // Если фильтр неизвестен - выбрасываем исключение
            default: {
                throw new \Exception("Unknown database filter");
            }
        }
        // Возвращаем отфильтрованный объект построителя запросов
        return $query;
    }

    /**
     * Функция-помощник для получения фильтра типа массив.
     *
     * @param Builder $query текущий объект построителя запросов.
     * @param string $dbColumn стообец в БД.
     * @param mixed $filter значение фильтра.
     * @return Builder обновленный объект построителя запросов.
     */
    private function getFilter(Builder $query, string $dbColumn, $filter)
    {
        // Если фильтр не по пустому значению - фильтруем, иначе - ищем где null
        return filled($filter[0]) ? $query->whereIn($dbColumn, $filter) : $query = $query->whereNull($dbColumn);
    }

    /**
     * Функция-помощник для сохранения фильтра-списка в БД.
     *
     * @param EventFiltersFormRequest $request запрос на сохранение фильтров.
     * @param string $fieldName имя фильтра, который сохраняется.
     */
    private function saveFilter(EventFiltersFormRequest $request, string $fieldName)
    {
        // Если отмечен чекбокс "все" - ничего не сохраняем.
        if($request->has($fieldName . '-all')) {
            return;
        }
        // Если в запросе нет указанного поля
        if(!$request->has($fieldName)) {
            // Создаем фильтр по пустому значению
            $filter = new EventFilter([
                'user_id' => Auth::user()->getAuthIdentifier(),
                'key' => $fieldName
            ]);
            $filter->save();
        } else {
            // Иначе - перебираем все отмеченные значения фильтра
            foreach ($request->get($fieldName) as $value) {
                // И сохраняем их
                $filter = new EventFilter([
                    'user_id' => Auth::user()->getAuthIdentifier(),
                    'key' => $fieldName,
                    'value' => $value
                ]);
                $filter->save();
            }
        }
    }

    /**
     * Функция-помощник для сохранения фильтра по дате в БД.
     *
     * @param EventFiltersFormRequest $request запрос на сохранение фильтров.
     * @param string $fieldName имя фильтра, который сохраняется.
     */
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
