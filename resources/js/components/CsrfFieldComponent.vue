<template>
    <input type="hidden" name="_token" :value="token">
</template>

<script>
    // Компонент csrf-поля для формы
    export default {
        // Данные
        data: function() {
            return {
                // Возвращает CSRF-токен
                token: $('meta[name="csrf-token"]').attr('content')
            };
        },
        name: "CsrfFieldComponent"
    }
</script>
