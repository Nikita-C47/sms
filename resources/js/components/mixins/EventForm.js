export default {
    props: {
        statuses: Object,
        relations: Array,
        departments: Array,
        types: Array,
        flights_url: String,
        categories_url: String,
        events_url: String,
        form_id: String
    },
    data: function() {
        return {
            loading: false,
            show_warning: false,
            flights: [],
            airports: [],
            categories: [],
            validation_errors: {},
            submit_url: window.location.href,
        };
    },
    methods: {
        updateFlights: function(date) {
            var selectedDate = moment(date).format('YYYY-MM-DD');
            this.show_warning = false;
            this.flights = [];
            var data = new FormData();
            data.append('date', selectedDate);

            axios.post(this.flights_url, data).then(response => {
                if(response.status === 200) {
                    if(response.data.flights.length === 0) {
                        this.show_warning = true;
                    } else {
                        this.flights = response.data.flights;
                    }
                }
            }).catch(error => {
                console.log(error);
            }).then(() => {
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
        updateAirports: function (flight_id) {
            this.unsetErrorField('flight_id');
            this.airports = [];
            if(flight_id !== "") {
                var flightIndex = _.findIndex(this.flights, ['id', flight_id]);
                var flight = this.flights[flightIndex];
                this.airports.push(flight.departure_airport, flight.arrival_airport);
            }
        },
        updateCategories: function (department_id) {
            this.unsetErrorField('department_id');
            this.categories = [];
            if(department_id !== null) {
                var data = new FormData();
                data.append('department_id', department_id);
                axios.post(this.categories_url, data).then(response => {
                    if(response.status === 200) {
                        this.categories = response.data.categories;
                    }
                }).catch(error => {
                    console.log(error);
                }).then(() => {
                    // post back if needed
                });
            }
        },
        submitForm: function () {
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


            this.loading = true;
            this.validation_errors = {};

            var data = new FormData($("#" + this.form_id)[0]);

            axios.post(this.submit_url, data).then(response => {
                if(response.status === 200) {
                    window.location = this.events_url;
                }
            }).catch(error => {
                if(error.response) {
                    if(error.response.status === 422) {
                        this.validation_errors = this.normalizeErrors(error.response.data.errors);
                        this.scrollToFirstError();
                    } else {
                        console.log(error.response);
                    }
                }
            }).then(() => {
                this.loading = false;
                loadingNotify.close();
            });
        },
    }
}
