<template>
    <span class="d-none"></span>
</template>

<script>
    // Компонент календаря для даты в форме события
    export default {
        name: "EventDatepickerComponent",
        // Свойства
        props: {
            // Инициализируемое поле
            field: String
        },
        // Данные
        data: function () {
            return {
                // Селектор
                selector: "#"+this.field
            }
        },
        // Хук монтирования компонента
        mounted() {
            // Инициализируем календарь
            $(this.selector).datepicker({
                language: "ru",
                maxDate: new Date(),
                autoClose: true,
                position: "bottom left",
                todayButton: new Date(),
                clearButton: true,
            });
        }
    }
</script>
