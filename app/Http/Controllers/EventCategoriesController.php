<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventCategoryFormRequest;
use App\Models\Department;
use App\Models\Events\EventCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Класс, представляющий контроллер категорий событий.
 * @package App\Http\Controllers Контроллеры приложения.
 */
class EventCategoriesController extends Controller
{
    /**
     * Отображает список категорий событий.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        // Начинаем конструировать запрос
        $entities = EventCategory::with('department');
        // Если это не администратор, значит список категорий запросил менеджер
        if(!Gate::allows('admin')) {
            /** @var \App\User $user */
            $user = Auth::user();
            // Добавляем фильтр по отделу
            $entities->where('department_id', $user->department_id);
        }
        // Добавляем сортировку и пагинатор
        $entities = $entities->orderBy('id', 'desc')->paginate(10);
        // Отображаем представление
        return view('event-categories.index', [
            'entities' => $entities
        ]);
    }

    /**
     * Отображает форму добавления категории.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        // Если это администратор - запрашиваем подразделения, менеджеру они не нужны
        $departments = Gate::allows('admin') ? Department::all() : null;
        // Возвращаем представление
        return view('event-categories.create', [
            'departments' => $departments
        ]);
    }

    /**
     * Сохраняет категорию в БД.
     *
     * @param EventCategoryFormRequest $request запрос на добавление категории.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(EventCategoryFormRequest $request)
    {
        /** @var \App\User $user */
        $user = Auth::user();
        // Создаем категорию
        $entity = new EventCategory([
            // Если это менеджер - проставляем его отдел
            'department_id' => Gate::allows('admin') ? $request->get('department_id') : $user->department_id,
            'code' => $request->get('code'),
            'name' => $request->get('name')
        ]);
        $entity->save();
        // Перенаправляем на страницу со списком категорий
        return redirect()->route('event-categories')->with('alert', [
            'type' => 'success',
            'text' => 'Новая категория событий успешно добавлена'
        ]);
    }

    /**
     * Отображает форму редактирования категории событий.
     *
     * @param int $id ID категории в БЛ.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        // Ищем категорию
        $entity = EventCategory::findOrFail($id);
        // Для администратора запрашиваем подразделения
        $departments = Gate::allows('admin') ? Department::all() : null;
        // Возвращаем представление
        return view('event-categories.edit', [
            'entity' => $entity,
            'departments' => $departments
        ]);
    }

    /**
     * Обновляет категорию в БД.
     *
     * @param EventCategoryFormRequest $request запрос на обновление категории.
     * @param int $id ID категории в БД.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(EventCategoryFormRequest $request, $id)
    {
        // Ищем категорию
        /** @var EventCategory $entity */
        $entity = EventCategory::findOrFail($id);
        // Проверяем, может ли пользователь редактировать категорию
        Gate::authorize('event-category', $entity);
        /** @var \App\User $user */
        $user = Auth::user();
        // Обновляем категорию
        $entity->fill([
            // Для менеджера указываем его подразделение
            'department_id' => Gate::allows('admin') ? $request->get('department_id') : $user->department_id,
            'code' => $request->get('code'),
            'name' => $request->get('name')
        ]);
        $entity->save();
        // Перенаправляем на список категорий с уведомлением
        return redirect()->route('event-categories')->with('alert', [
            'type' => 'success',
            'text' => 'Категория событий №'.$entity->id.' успешно обновлена'
        ]);
    }

    /**
     * Удаляет категорию из БД.
     *
     * @param int $id ID подразделения в БД.
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception исключение в случае неудачного удаления.
     */
    public function destroy($id)
    {
        // Ищем категорию в БД
        /** @var EventCategory $entity */
        $entity = EventCategory::findOrFail($id);
        // Проверяем, может ли пользователь редактировать категорию
        Gate::authorize('event-category', $entity);
        // Удаляем категорию
        $entity->delete();
        // Перенаправляем на список категорий с уведомлением
        return redirect()->route('event-categories')->with('alert', [
            'type' => 'success',
            'text' => 'Категория событий "'.$entity->name.'" успешно удалена'
        ]);
    }
}
