// Примеси-помощники
export default {
    // Методы
    methods: {
        // Смена инпута с файлами
        changeFileInput: function (event) {
            // Сбрасываем поля с ошибкой
            this.unsetErrorField('attachments');
            // Получаем файлы
            var element = event.target;
            var files = element.files;
            // Ищем метку
            var placeholder = $("label[for='"+element.id+"']");
            // Если файлов нет - сбрасываем на метку по-умолчанию
            if(files.length === 0) {
                var defaultPlaceholder = "Выберите файлы";
                placeholder.text(defaultPlaceholder);
            } else {
                // Если есть
                if(files.length === 1) {
                    // Для одного файла
                    placeholder.text(files[0].name);
                } else {
                    // Для нескольких файлов
                    placeholder.text('Выбрано файлов: '+files.length);
                }
            }
        },
        // Выводит пустое значение или значение
        emptyOrValue: function(value) {
            return value === null ? "" : value;
        },
        // Генерирует идентификатор
        getIdentifier: function(prefix, id) {
            return prefix + id;
        },
    }
}
