// Примеси для валидации
export default {
    // Данные
    data: function() {
        return {
            // Ошибки валидации
            validation_errors: {}
        };
    },
    // Методы
    methods: {
        // Проверяет, есть ли ошибка у поля
        hasError: function (fieldName) {
            return _.hasIn(this.validation_errors, fieldName);
        },
        // Получает текст ошибки
        errorText: function (fieldName, multiple = false) {
            if(this.hasError(fieldName)) {
                return multiple ? this.validation_errors[fieldName] : this.validation_errors[fieldName][0];
            }
            return "";
        },
        // Сбрасывает поле с ошибкой
        unsetErrorField: function (fieldName) {
            if(this.hasError(fieldName)) {
                this.$delete(this.validation_errors, fieldName);
            }
        },
        // Получает классы поля на основе наличия ошибки
        fieldClasses: function(fieldName, defaultClasses = 'form-control') {
            // Добавляем классы по-умолчанию
            var classes = [defaultClasses];
            // Если есть ошибка - добавляем класс ошибки
            if(this.hasError(fieldName)) {
                classes.push('is-invalid');
            }
            // Возвращаем классы
            return classes;
        },
        // Прокручивает страницу к первой ошибке
        scrollToFirstError: function() {
            var errorsChecker = setInterval(function () {
                var firstError = $(".invalid-feedback:first");
                if(firstError.length === 1) {
                    $('html, body').animate({
                        scrollTop: firstError.parents('.form-group:first').offset().top - 70
                    }, 1000);
                    clearInterval(errorsChecker);
                }
            });
        },
        // Нормализует список ошибок
        normalizeErrors: function(errorsObject) {
            // Перебираем ошибки
            _.forEach(errorsObject, function (value, key) {
                // Ищем ошибки через точку
                var splittedKey = _.split(key, '.');
                // Заполняем родительскую ошибку (нужно так как Laravel присылает для массивов ошибки через точку)
                if(splittedKey.length > 1) {
                    if(_.hasIn(errorsObject, splittedKey[0])) {
                        errorsObject[splittedKey[0]].push(value);
                    } else {
                        errorsObject[splittedKey[0]] = [value];
                    }
                }
            });
            // Возвращаем измененный объект
            return errorsObject;
        },
    }
}
