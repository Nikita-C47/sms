// Примеси с данными для форм событий
export default {
    // Свойства
    props: {
        // Статусы события
        statuses: Object,
        // Мероприятия, к которым относится событие
        relations: Array,
        // Подразделения
        departments: Array,
        // Типы событий
        types: Array,
        // Адрес получения списка рейсов по дате
        flights_url: String,
        // Адрес получения категорий события по подразделению
        categories_url: String,
        // Адрес списка событий
        events_url: String,
        // ID формы
        form_id: String
    },
    // Данные
    data: function() {
        return {
            // Флаг загрузки
            loading: false,
            // Флаг показа предупреждения о пустом списке рейсов на дату
            show_warning: false,
            // Рейсы
            flights: [],
            // Аэропорты
            airports: [],
            // Категории
            categories: [],
            // Адрес отправки формы
            submit_url: window.location.href,
        };
    },
    // Методы
    methods: {
        // Обновление рейсов
        updateFlights: function(date) {
            // Форматируем дату
            var selectedDate = moment(date).format('YYYY-MM-DD');
            // Сбрасываем показ предупреждения и список рейсов
            this.show_warning = false;
            this.flights = [];
            // Формируем параметры запроса
            var data = new FormData();
            data.append('date', selectedDate);
            // Отправляем запрос
            axios.post(this.flights_url, data).then(response => {
                // Если все в порядке
                if(response.status === 200) {
                    // Если рейсов на дату нет - показываем предупреждение
                    if(response.data.flights.length === 0) {
                        this.show_warning = true;
                    } else {
                        // Ксли есть - заполняем массив
                        this.flights = response.data.flights;
                    }
                }
            }).catch(error => {
                // Обработчик ошибки
                console.log(error);
            }).then(() => {
                // Если надо показать предупреждение - показываем
                if(this.show_warning) {
                    var content = {
                        message: "На указанную дату рейсы отсутствуют",
                        title: "Внимание!",
                        icon: "fas fa-exclamation",
                    };

                    $.notify(content,{
                        type: 'warning',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                        delay: 3000,
                    });
                }
            });
        },
        // Обновление аэропортов
        updateAirports: function (flight_id) {
            // Сбрасываем ошибку
            this.unsetErrorField('flight_id');
            // Сбрасываем список аэропортов
            this.airports = [];
            // Если указан рейс
            if(flight_id !== "") {
                // Ищем его
                var flightIndex = _.findIndex(this.flights, ['id', flight_id]);
                // Получаем
                var flight = this.flights[flightIndex];
                // Добавляем аэропорты данного рейса в список выбора
                this.airports.push(flight.departure_airport, flight.arrival_airport);
            }
        },
        // Обновление категорий события
        updateCategories: function (department_id) {
            // Сбрасываем ошибку и список категорий
            this.unsetErrorField('department_id');
            this.categories = [];
            // Если указан отдел
            if(department_id !== null) {
                // Формируем данные
                var data = new FormData();
                data.append('department_id', department_id);
                // Отправляем запрос
                axios.post(this.categories_url, data).then(response => {
                    // Если все в порядке - заполняем список категорий
                    if(response.status === 200) {
                        this.categories = response.data.categories;
                    }
                }).catch(error => {
                    // Обработчик ошибки
                    console.log(error);
                }).then(() => {
                    // post back if needed
                });
            }
        },
        // Сохранение события
        submitForm: function () {
            // Показываем уведомление о загрузке
            var loadingNotify = $.notify(
                {
                    message: "Идет проверка и сохранение данных...",
                    title: "Подождите",
                    icon: "fas fa-exclamation",
                },
                {
                    type: 'warning',
                    allow_dismiss: false,
                    placement: {
                        from: 'top',
                        align: 'right'
                    },
                    delay: 0,
                }
            );
            // Устанавливаем флаг загрузки
            this.loading = true;
            // Сбрасываем ошибки валидации
            this.validation_errors = {};
            // Собираем данные формы
            var data = new FormData($("#" + this.form_id)[0]);
            // Отправляем запрос
            axios.post(this.submit_url, data).then(response => {
                // Если все в порядке - отправляем на список событий
                if(response.status === 200) {
                    window.location = this.events_url;
                }
            }).catch(error => {
                // Обработчик ошибки
                if(error.response) {
                    // Если это валидация
                    if(error.response.status === 422) {
                        // Нормальзуем ошибки
                        this.validation_errors = this.normalizeErrors(error.response.data.errors);
                        // И прокручиваем к первой
                        this.scrollToFirstError();
                    } else {
                        // Иначе - пишем ошибку в консоль
                        console.log(error.response);
                    }
                }
            }).then(() => {
                // Убираем загрузку
                this.loading = false;
                // Закрываем уведомление о загрузке
                loadingNotify.close();
            });
        },
    }
}
