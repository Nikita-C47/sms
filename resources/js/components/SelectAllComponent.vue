<template>
    <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox"
               :id="identifier"
               :name="identifier"
               v-model="is_checked"
               @change="setCheckboxes()"
               class="custom-control-input">
        <label class="custom-control-label" :for="identifier">
            Все
        </label>
    </div>
</template>

<script>
    // Компонент чекбокса "Выбрать все" в форме фильтров списка событий
    export default {
        name: "SelectAllComponent",
        // Свойства
        props: {
            // Название элементов чекбокса
            name: String,
            // Флаг того что чекбокс отмечен
            checked: Number
        },
        // Вычисляемые свойства
        computed: {
            // Идентификатор чекбокса
            identifier: function () {
                return this.name + '-all';
            },
            // Селектор связанных чекбоксов
            selector: function () {
                return "[name='"+this.name+"[]']";
            }
        },
        // данные
        data: function () {
            return {
                // Флаг того что чекбокс отмечен
                is_checked: null
            }
        },
        // Методы
        methods: {
            // Установка чекбоксов
            setCheckboxes: function () {
                // Выбираем чекбоксы
                var checkboxes = $(this.selector);
                // Устанавливаем значение чекбокса "Выбрать все"
                checkboxes.prop('checked', this.is_checked);
            }
        },
        // хук монтирования компонента
        mounted() {
            // Флаг того что чекбокс отмечен
            this.is_checked = this.checked > 0;
            // Если чекбокс отмечен - сразу отмечаем все
            var checkboxes = $(this.selector);
            if(this.is_checked) {
                checkboxes.prop('checked', true);
            }
            // Обработчик клика по чекбоксам, к которым привязан данный чекбокс
            checkboxes.click(() => {
                var select_all = $("#"+this.identifier);
                // Если один из чекбоксов снимают, чекбокс "выбрать все" тоже снимается
                if(select_all.prop("checked")) {
                    this.is_checked = false;
                }

            });
        }
    }
</script>
