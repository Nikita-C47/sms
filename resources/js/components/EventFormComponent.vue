<template>
    <form :id="form_id">
        <div class="form-group px-0">
            <label for="date" class="font-weight-bold">Дата события: <span class="text-danger">*</span></label>
            <input :class="fieldClasses('date')"
                   @change="unsetErrorField('date')"
                   :disabled="loading"
                   placeholder="Укажите дату события"
                   type="text"
                   required
                   name="date"
                   id="date"
                   readonly>
            <div v-if="hasError('date')" class="invalid-feedback">
                {{ errorText('date') }}
            </div>
        </div>
        <div class="form-group px-0 pb-0">
            <div class="custom-control custom-checkbox">
                <input type="checkbox"
                       class="custom-control-input"
                       :disabled="loading || flights.length === 0"
                       v-model="flight_connection"
                       id="flight_connection"
                       name="flight_connection"
                       value="1">
                <label class="custom-control-label" for="flight_connection">Связать с рейсом</label>
            </div>
        </div>
        <div v-if="flight_connection">
            <div class="form-group px-0">
                <label class="font-weight-bold" for="flight_id">
                    Рейс:
                </label>
                <select :class="fieldClasses('flight_id')"
                        :disabled="loading"
                        @change="updateAirports(flight_id)"
                        name="flight_id"
                        id="flight_id"
                        v-model="flight_id">
                    <option value="">- Укажите рейс -</option>
                    <option v-for="flight in flights" :value="flight.id">
                        {{ flight.departure_airport }} - {{ flight.arrival_airport }} ({{ flight.number }}, {{ flight.departure_date}})
                    </option>
                </select>
                <div v-if="hasError('flight_id')" class="invalid-feedback">
                    {{ errorText('flight_id') }}
                </div>
            </div>
            <div class="form-group px-0">
                <label for="airport" class="font-weight-bold">
                    Где произошло:
                </label>
                <select id="airport"
                        name="airport"
                        class="form-control input-solid"
                        :disabled="airports.length === 0 || loading"
                        v-model="airport">
                    <option value="">- Укажите где произошло событие -</option>
                    <option v-for="airport in airports" :value="airport">
                        {{ airport }}
                    </option>
                </select>
            </div>
        </div>
        <div v-if="!flight_connection" class="form-group px-0">
            <label for="airportText" class="font-weight-bold">
                Где произошло:
            </label>
            <input type="text"
                   id="airportText"
                   name="airport"
                   placeholder="Укажите где произошло событие"
                   class="form-control input-solid"/>
        </div>
        <div class="form-group px-0">
            <label class="font-weight-bold" for="initiator">
                От кого сообщение:
            </label>
            <input class="form-control input-solid"
                   :disabled="loading"
                   type="text"
                   name="initiator"
                   id="initiator"
                   placeholder="Укажите кто сообщил о событии"/>
        </div>
        <div class="form-group px-0">
            <label class="font-weight-bold" for="relation_id">
                Относится к:
            </label>
            <select :class="fieldClasses('relation')"
                    @change="unsetErrorField('relation_id')"
                    id="relation_id"
                    name="relation_id"
                    :disabled="loading">
                <option value="">- Укажите мероприятие, к которому относится событие -</option>
                <option v-for="relation in relations" :value="relation.id">
                    {{ relation.name }}
                </option>
            </select>
            <div v-if="hasError('relation_id')" class="invalid-feedback">
                {{ errorText('relation_id') }}
            </div>
        </div>
        <div class="form-group px-0">
            <label class="font-weight-bold" for="department_id">
                Подразделение:
            </label>
            <select :class="fieldClasses('department_id')"
                    id="department_id"
                    :disabled="loading"
                    name="department_id"
                    @change="updateCategories(department_id)"
                    v-model="department_id">
                <option value="">- Укажите подразделение -</option>
                <option v-for="department in departments" :value="department.id">
                    {{ department.name }}
                </option>
            </select>
            <div v-if="hasError('department_id')" class="invalid-feedback">
                {{ errorText('department_id') }}
            </div>
        </div>
        <div class="form-group px-0">
            <label class="font-weight-bold" for="category_id">
                Категория события:
            </label>
            <select :class="fieldClasses('category_id')"
                    id="category_id"
                    name="category_id"
                    @change="unsetErrorField('category_id')"
                    :disabled="categories.length === 0 || loading">
                <option value="">- Укажите категорию события -</option>
                <option v-for="category in categories" :value="category.id">
                    {{ category.code }} - {{ category.name }}
                </option>
            </select>
            <div v-if="hasError('category_id')" class="invalid-feedback">
                {{ errorText('category_id') }}
            </div>
        </div>
        <div class="form-group px-0">
            <label class="font-weight-bold" for="type_id">
                Тип:
            </label>
            <select :class="fieldClasses('type_id')"
                    @change="unsetErrorField('type_id')"
                    id="type_id"
                    :disabled="loading"
                    name="type_id">
                <option value="">- Укажите тип события -</option>
                <option v-for="type in types" :value="type.id">
                    {{ type.name }}
                </option>
            </select>
            <div v-if="hasError('type_id')" class="invalid-feedback">
                {{ errorText('type_id') }}
            </div>
        </div>
        <div class="form-group px-0">
            <label class="font-weight-bold">
                Прикрепить файлы:
            </label>
            <div class="custom-file">
                <input type="file"
                       :class="fieldClasses('attachments', 'custom-file-input')"
                       id="attachments"
                       name="attachments[]"
                       @change="changeFileInput($event)"
                       :disabled="loading"
                       placeholder="..."
                       required multiple>
                <label class="custom-file-label" for="attachments" data-browse="Обзор">
                    Выберите файлы
                </label>
                <div v-if="hasError('attachments')" class="invalid-feedback">
                    Список ошибок:
                    <ul>
                        <li v-for="error in errorText('attachments')">
                            {{ error }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="form-group px-0">
            <label for="message" class="font-weight-bold">Сообщение: <span class="text-danger">*</span></label>
            <textarea rows="6"
                      :disabled="loading"
                      :class="fieldClasses('message')"
                      @change="unsetErrorField('message')"
                      id="message"
                      name="message"
                      placeholder="Опишите случившееся событие"></textarea>
            <div v-if="hasError('message')" class="invalid-feedback">
                {{ errorText('message') }}
            </div>
        </div>
        <div class="form-group px-0">
            <label for="commentary" class="font-weight-bold">Комментарий:</label>
            <textarea rows="6"
                      :disabled="loading"
                      class="form-control input-solid"
                      id="commentary"
                      name="commentary"
                      placeholder="Вы можете добавить произвольный комментарий к событию"></textarea>
        </div>
        <div class="form-group px-0">
            <label class="font-weight-bold" for="status">
                Статус события:
            </label>
            <select class="form-control input-solid"
                    id="status"
                    aria-describedby="statusHelp"
                    :disabled="loading"
                    name="status">
                <option v-for="(status, code) in statuses" :value="code">
                    {{ status }}
                </option>
            </select>
            <small id="statusHelp">
                Если событие добавляется уже после реакции на него, можно сразу указать решено оно или нет.
            </small>
        </div>
        <div class="form-group px-0">
            <button type="submit" class="btn btn-success" @click.prevent="submitForm" :disabled="loading">
                Добавить событие
            </button>
            <!-- TODO: Пофиксить окраску ссылок в синий цвет -->
            <a :href="events_url" class="btn btn-primary">
                К списку событий
            </a>
        </div>
    </form>
</template>

<script>
    import Helpers from './mixins/Helpers';
    import Validation from './mixins/Validation';
    import EventForm from './mixins/EventForm';

    export default {
        name: "EventFormComponent",
        mixins:[
            Helpers,
            Validation,
            EventForm
        ],
        props: {
            form_id: String
        },
        data: function () {
            return {
                flight_connection: false,
                flight_id: "",
                airport: "",
                department_id: ""
            }
        },
        mounted() {
            $("#date").datepicker({
                language: "ru",
                maxDate: new Date(),
                autoClose: true,
                position: "bottom left",
                todayButton: new Date(),
                clearButton: true,
                onSelect: (formattedDate, date, inst) => {
                    this.updateFlights(date);
                    this.unsetErrorField('date');
                }
            });
        }
    }
</script>
