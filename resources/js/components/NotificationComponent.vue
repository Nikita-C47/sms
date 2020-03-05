<template>
    <div class="d-none"></div>
</template>

<script>
    // Компонент отправки уведомления
    export default {
        name: "NotificationComponent",
        // Свойства
        props: {
            // Тип
            type: String,
            // Сообщение
            message: String
        },
        // Данные
        data: function() {
            return {
                // Иконки для типов уведомлений
                icons: {
                    success: 'fas fa-check',
                    danger: 'fas fa-times',
                    warning: 'fas fa-exclamation',
                    primary: 'fas fa-bell',
                    secondary: 'fas fa-bullhorn'
                }
            }
        },
        // Хук монтирования компонента
        mounted() {
            // Заполняем контент
            var content = {
                message: this.message,
                title: "Внимание!",
                icon: this.icons[this.type],
            };
            // Показываем уведомление
            $.notify(content,{
                type: this.type,
                placement: {
                    from: 'top',
                    align: 'right'
                },
                delay: 3000,
            });
        }
    }
</script>

<style scoped>

</style>
