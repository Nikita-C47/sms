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
    export default {
        name: "DatesRangeComponent",
        props: {
            start_date_value: String,
            finish_date_value: String
        },
        data: function() {
            return {
                start_date: null,
                finish_date: null
            }
        },
        mounted() {
            this.start_date = $("#date_from").datepicker({
                language: "ru",
                maxDate: new Date(),
                autoClose: true,
                position: "bottom left",
                todayButton: new Date(),
                clearButton: true,
                onSelect: (formattedDate, date, inst) => {
                    this.finish_date.update("minDate", date);
                }
            }).data('datepicker');

            this.finish_date = $("#date_to").datepicker({
                language: "ru",
                maxDate: new Date(),
                autoClose: true,
                position: "bottom left",
                todayButton: new Date(),
                clearButton: true,
                onSelect: (formattedDate, date, inst) => {
                    date = formattedDate.length > 0 ? date : new Date();
                    this.start_date.update("maxDate", date);
                }
            }).data('datepicker');

            if(this.start_date_value.length > 0) {
                this.start_date.selectDate(new Date(this.start_date_value));
            }

            if(this.finish_date_value.length > 0) {
                this.finish_date.selectDate(new Date(this.finish_date_value));
            }
        }
    }
</script>

<style scoped>

</style>
