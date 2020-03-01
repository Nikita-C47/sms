<?php

namespace App\Http\Controllers;

use App\Components\Services\EventUpdatedNotifier;
use App\Events\ResponsibleDepartmentAdded;
use App\Events\ResponsibleDepartmentRemoved;
use App\Http\Requests\Events\AnonymousEventFormRequest;
use App\Http\Requests\Events\EventCategoriesFormRequest;
use App\Http\Requests\Events\FindEventFormRequest;
use App\Http\Requests\Events\FlightsFormRequest;
use App\Http\Requests\Events\EventFormRequest;
use App\Http\Requests\Events\ProcessEventFormRequest;
use App\Jobs\SendEventNotificationsJob;
use App\Models\Department;
use App\Models\Events\Event;
use App\Models\Events\EventAttachment;
use App\Models\Events\EventCategory;
use App\Models\Events\EventMeasure;
use App\Models\Events\EventRelation;
use App\Models\Events\EventResponsibleDepartment;
use App\Models\Events\EventType;
use App\Models\Flight;
use App\Notifications\EventProcessed;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
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

        $events = Event::approved()->with([
            'flight',
            'relation',
            'department',
            'user_created_by',
            'responsible_departments'
        ])->orderBy('updated_at', 'desc');

        if($user->access_level === 'user') {
            $events = $events->where('created_by', $user->id);
        }

        $events = $events->paginate(10);

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

    public function findById(FindEventFormRequest $request)
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

        return view('events.search', [
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
        DB::transaction(function () use ($request) {
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
            'text' => 'Новое событие успешно добавлено'
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
            $event->save();

            return $event;
        });

        $users = User::whereIn('access_level', ['admin', 'manager'])->where('id', '<>', Auth::user()->getAuthIdentifier())->get();
        Notification::send($users, new EventProcessed($event));

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
            'attachments'
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
//        $blackfire = new \Blackfire\Client();
//
//        $probe = $blackfire->createProbe();

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
            // Удаляем существующие мероприятия по ключам
            EventMeasure::destroy($removalArray);

            // Получаем ключи вложений, которые нужно удалить
            $removalArray = $this->getMultipleKeys($request, 'removed_attachment_', 'removed_attachments_count');
            // Получаем указанные вложения
            $attachmentsToRemove = EventAttachment::sharedLock()->find($removalArray);
            // Перебираем их
            /** @var EventAttachment $attachment */
            foreach ($attachmentsToRemove as $attachment) {
                // Удаляем вложение
                $attachment->delete();
                // Удаляем его из файловой системы
                $attachment->removeFromFileSystem();
            }

            // Возвращаем обновленное событие
            return $event;
        });

        /*
        SendEventNotificationsJob::dispatch(
            EventUpdatedNotifier::class,
            $event,
            [
                'added_departments' => $newRDs->diff($existingRDs)->toArray(),
                'removed_departments' => $existingRDs->diff($newRDs)->toArray()
            ]
        );
        */

        // 4. Генерируем события

        // Событие, инициирующее отправку уведомлений пользователям новых ответственных подразделений
        event(new ResponsibleDepartmentAdded($newRDs->diff($existingRDs)->toArray(), $event));
        // Событие, инициирующее отправку уведомлений пользователям удаленных ответственных подразделений
        event(new ResponsibleDepartmentRemoved($existingRDs->diff($newRDs)->toArray(), $event));

        // Добавляем флэш-уведомление в сессию
        $request->session()->flash('alert', [
            'type' => 'success',
            'text' => 'Событие №'.$id.' успешно обновлено'
        ]);

        //$blackfire->endProbe($probe);

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
            $event->forceDelete();
            return $event;
        });

        Storage::disk('public')->deleteDirectory('events/'.$event->id);

        return redirect()->route('home')->with('alert', [
            'type' => 'success',
            'text' => 'Событие №'.$event->id.' безвозвратно удалено из базы данных'
        ]);
    }

    // TODO: Добавить интерфейс для редактирования категорий менеджером событий
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
