<template>
    <span class="d-none"></span>
</template>

<script>
    export default {
        name: "EventDatepickerComponent",
        props: {
            field: String
        },
        data: function () {
            return {
                selector: "#"+this.field
            }
        },
        mounted() {
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
