<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\Department;
use App\Notifications\UserCredentialsNotification;
use App\User;
use App\Components\Helpers\PasswordHelper;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\{Auth, Hash, Log};

/**
 * Класс, представляющий контроллер рейсов.
 * @package App\Http\Controllers Контроллеры приложения.
 */
class UsersController extends Controller
{
    use PasswordHelper;
    /**
     * Отображает список пользователей.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Выбираем настоящих пользователей и отображаем по 10 штук на странице.
        $users = User::real()->with('department')->orderBy('id', 'desc')->paginate(10);
        return view('users.index', [
            'users' => $users
        ]);
    }

    /**
     * Отображает форму создания пользователя.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Получаем список отделов.
        $departments = Department::all();
        // Возвращаем представление
        return view('users.create', [
            'departments' => $departments,
            'roles' => User::USER_ROLES
        ]);
    }

    /**
     * Сохраняет пользователя в БД.
     *
     * @param UserFormRequest $request запрос на добавление пользователя.
     * @return \Illuminate\Http\Response
     */
    public function store(UserFormRequest $request)
    {
        // Генерируем пользователю пароль
        $password = $this->generatePassword();
        // Создаем нового пользователя
        $user = new User([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'access_level' => $request->get('access_level'),
            'department_id' => $request->get('department_id'),
            'password' => Hash::make($password)
        ]);
        $user->save();
        // Отправляем ему уведомление о логине и пароле
        $user->notify(new UserCredentialsNotification($password));
        /** @var \App\User $authUser */
        $authUser = Auth::user();
        // Пишем в лог сообщение о создании пользователя
        Log::channel('user_actions')
            ->info("User ".$user->name." was created by user ".$authUser->name);
        // Возвращаем редирект на страницу со списком пользователей
        return redirect()->route('users')->with('alert', [
            'type' => 'success',
            'text' => 'Новый пользователь успешно добавлен. Пароль выслан на указанный email'
        ]);
    }

    /**
     * Отображает форму редактирования пользователя.
     *
     * @param int $id ID пользователя.
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Ищем пользователя среди реальных
        $user = User::real()->with('department')->findOrFail($id);
        // Получаем список подразделений
        $departments = Department::all();
        // Отображаем форму редактирования
        return view('users.edit', [
            'user' => $user,
            'departments' => $departments,
            'roles' => User::USER_ROLES
        ]);
    }

    /**
     * Обновляет пользователя в БД.
     *
     * @param UserFormRequest $request запрос на обновление пользователя.
     * @param int $id ID пользователя в БД.
     * @return \Illuminate\Http\Response
     */
    public function update(UserFormRequest $request, $id)
    {
        // Ищем пользователя среди реальных
        /** @var User $user */
        $user = User::real()->findOrFail($id);
        // Обновляем его
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->access_level = $request->get('access_level');
        $user->department_id = $request->get('department_id');
        $user->save();
        // Задаем текст для уведомления
        $text = "Пользователь успешно обновлен";
        /** @var \App\User $authUser */
        $authUser = Auth::user();
        // Если нужно обновить пароль
        if($request->has('reset_password')) {
            // Генерируем пароль
            $password = $this->generatePassword();
            // Устанавливаем его
            $user->password = Hash::make($password);
            $user->save();
            // Отправляем новые учетные данные
            $user->notify(new UserCredentialsNotification($password, true));
            // Дописываем увдомление
            $text .= ". Новый пароль выслан на указанный email";
            // Пишем в лог что пользователь обновил пароль
            Log::channel('user_actions')
                ->info("User ".$authUser->name." resets user ".$user->name." password");
        }
        // Пишем в лог сообщение о обновлении пользователя
        Log::channel('user_actions')->info("User ".$user->name." was updated by user ".$authUser->name);
        // Возвращаем редирект на список пользователей с уведомлением
        return redirect()->route('users')->with('alert', [
            'type' => 'success',
            'text' => $text
        ]);
    }

    /**
     * Удаляет пользователя из БД.
     *
     * @param int $id ID пользователя в БД.
     * @return \Illuminate\Http\Response
     * @throws \Exception исклбчение при неудачном удалении.
     */
    public function destroy($id)
    {
        // Тщем пользователя
        /** @var User $user */
        $user = User::real()->findOrFail($id);
        // Удаляем его
        $user->delete();
        /** @var \App\User $authUser */
        $authUser = Auth::user();
        // Пишем в лог сообщение об удалении пользователя
        Log::channel('user_actions')
            ->info("User ".$user->name." was removed by user ".$authUser->name);
        // Возвращаем редирект на страницу со списокм пользователей
        return redirect()->route('users')->with('alert', [
            'type' => 'success',
            'text' => 'Пользователь "'.$user->name.'" успешно удалён!'
        ]);
    }

    /**
     * Авторизует пользователя под указанным пользователем.
     *
     * @param int $id ID пользователя в БД.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function auth($id)
    {
        // Получаем текущего пользователя
        /** @var \App\User $authUser */
        $authUser = Auth::user();
        $authUser = $authUser->name;
        // Ищем нужного пользователя среди реальных
        /** @var Authenticatable|User $user */
        $user = User::real()->findOrFail($id);
        // Авторизуем пользователя под другим пользователем
        Auth::login($user);
        // Пишем в лог сообщение об авторизации пользователя под другим
        Log::channel('user_actions')
            ->info("User ".$authUser." was authorized as user ".$user->name);
        // Возвращаем редирект на главную страницу
        return redirect()->route('home');
    }
}
