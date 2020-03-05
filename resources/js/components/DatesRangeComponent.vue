<template>
    <div>
        <input class="form-control"
               placeholder="Укажите начальную дату"
               type="text"
               name="date_from"
               id="date_from"
               readonly>
        <br>
        <input class="form-control"
               placeholder="Укажите конечную дату"
               type="text"
               name="date_to"
               id="date_to"
               readonly>
    </div>
</template>

<script>
    // Компонент связанных календарей
    export default {
        name: "DatesRangeComponent",
        // Свойства
        props: {
            // Начальная дата
            start_date_value: String,
            // Конечная дата
            finish_date_value: String
        },
        // Данные
        data: function() {
            return {
                // Объект datepicker-а начальной даты
                start_date: null,
                // Объект datepicker-а конечной даты
                finish_date: null
            }
        },
        // Хук монтирования компонента
        mounted() {
            // Инициализируем начальную дату
            this.start_date = $("#date_from").datepicker({
                language: "ru",
                maxDate: new Date(),
                autoClose: true,
                position: "bottom left",
                todayButton: new Date(),
                clearButton: true,
                onSelect: (formattedDate, date, inst) => {
                    // При выборе устанавливаем минимальную дату в конечной дате
                    this.finish_date.update("minDate", date);
                }
            }).data('datepicker');

            // Инициализируем конечную дату
            this.finish_date = $("#date_to").datepicker({
                language: "ru",
                maxDate: new Date(),
                autoClose: true,
                position: "bottom left",
                todayButton: new Date(),
                clearButton: true,
                onSelect: (formattedDate, date, inst) => {
                    // При выборе, если это очистка даты - снова устанавливаем максимальной датой
                    // начальной даты сегодняшнюю, иначе - выбранную
                    date = formattedDate.length > 0 ? date : new Date();
                    this.start_date.update("maxDate", date);
                }
            }).data('datepicker');

            // Если даты указаны - инициализируем их значениями

            if(this.start_date_value.length > 0) {
                this.start_date.selectDate(new Date(this.start_date_value));
            }

            if(this.finish_date_value.length > 0) {
                this.finish_date.selectDate(new Date(this.finish_date_value));
            }
        }
    }
</script>
