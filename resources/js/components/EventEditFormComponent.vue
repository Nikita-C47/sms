<template>
    <form :id="form_id" method="post">
        <input type="hidden" name="event_id" :value="event.id">
    <div class="row">
        <div class="col-lg-6 col-sm-12">
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
                           :disabled="loading || flights.length === 0"
                           v-model="flight_connection"
                           class="custom-control-input"
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
                            @change="updateAirports(form_data.flight_id)"
                            name="flight_id"
                            id="flight_id"
                            v-model="form_data.flight_id">
                        <option value="">- Укажите рейс -</option>
                        <option v-for="flight in flights" :value="flight.id">
                            {{ flight.departure_airport }} - {{ flight.arrival_airport }} ({{ flight.number }}, {{ flight.board }},z {{ flight.departure_date}})
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
                            v-model="form_data.airport">
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
                       v-model="form_data.airport"
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
                       v-model="form_data.initiator"
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
                        v-model="form_data.relation_id"
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
                        @change="updateCategories(form_data.department_id)"
                        v-model="form_data.department_id">
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
                        v-model="form_data.category_id"
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
                        v-model="form_data.type_id"
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
                <div v-if="form_data.attachments.length > 0">
                    <label class="font-weight-bold mt-3">
                        Открепить файлы:
                    </label>
                    <br>
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle"></i> Файлы будут откреплены от события только после его сохранения.
                    </div>
                </div>
                <div class="row mt-2">
                    <div v-for="attachment in form_data.attachments" class="col-lg-6 col-sm-12">
                        <div class="card mb-3 border-dark">
                            <div class="card-header border-dark">
                                <a :href="attachment.link" target="_blank">
                                    {{ attachment.original_name }} ({{ attachment.size_text }})
                                </a>
                                <button v-if="attachmentRemoved(attachment.id)"
                                        type="button"
                                        class="close"
                                        aria-label="Restore"
                                        @click="restoreAttachment(attachment.id)">
                                    <i class="fas fa-redo fa-xs"></i>
                                </button>
                                <button v-else
                                        type="button"
                                        class="close"
                                        aria-label="Close"
                                        @click="removeAttachment(attachment.id)">
                                    <i class="fas fa-times fa-xs"></i>
                                </button>
                            </div>
                            <div class="card-footer text-muted">
                                {{ attachment.user_created_by.name }} ({{ attachment.created_at_display }})
                            </div>
                        </div>
                    </div>
                    <input v-for="(id, index) in removed_attachments" type="hidden" :name="getIdentifier('removed_attachment_', index)" :value="id">
                    <input type="hidden" name="removed_attachments_count" :value="removed_attachments.length">
                </div>
            </div>
            <div class="form-group px-0">
                <label for="message" class="font-weight-bold">Сообщение: <span class="text-danger">*</span></label>
                <textarea rows="6"
                          :disabled="loading"
                          :class="fieldClasses('message')"
                          @change="unsetErrorField('message')"
                          v-model="form_data.message"
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
                          v-model="form_data.commentary"
                          class="form-control input-solid"
                          id="commentary"
                          name="commentary"
                          placeholder="Вы можете добавить произвольный комментарий к событию"></textarea>
            </div>
        </div>
        <div class="col-lg-6 col-sm-12">
            <div class="form-group px-0">
                <label class="font-weight-bold" for="status">
                    Статус события:
                </label>
                <select class="form-control input-solid"
                        id="status"
                        v-model="form_data.status"
                        :disabled="loading"
                        name="status">
                    <option v-for="(status, code) in statuses" :value="code">
                        {{ status }}
                    </option>
                </select>
            </div>
            <div class="form-group px-0">
                <label for="approved" class="font-weight-bold">Одобрено:</label>
                <br>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio"
                           id="approved"
                           name="approved"
                           class="custom-control-input"
                           value="1"
                           v-model="form_data.approved">
                    <label class="custom-control-label font-weight-normal" for="approved">Одобрено</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio"
                           id="not_approved"
                           name="approved"
                           class="custom-control-input"
                           value="0"
                           v-model="form_data.approved">
                    <label class="custom-control-label font-weight-normal" for="not_approved">Не одобрено</label>
                </div>
                <br>
                <small id="approvedHelp">
                    В списке событий отображаются только одобренные.
                </small>
            </div>
            <div class="form-group px-0">
                <label for="reason" class="font-weight-bold">Выявленная причина:</label>
                <textarea rows="6"
                          :disabled="loading"
                          v-model="form_data.reason"
                          class="form-control input-solid"
                          id="reason"
                          name="reason"
                          placeholder="Здесь Вы можете указать выявленную причину происшествия"></textarea>
            </div>
            <div class="form-group px-0">
                <label for="decision" class="font-weight-bold">Принятое решение:</label>
                <textarea rows="6"
                          :disabled="loading"
                          v-model="form_data.decision"
                          class="form-control input-solid"
                          id="decision"
                          name="decision"
                          placeholder="Тут можно описать принятое на основании события решение"></textarea>
            </div>
            <div class="form-group px-0">
                <label for="fix_date" class="font-weight-bold">Дата устранения:</label>
                <input :class="fieldClasses('fix_date')"
                       @change="unsetErrorField('fix_date')"
                       :disabled="loading"
                       placeholder="Укажите дату устранения события"
                       type="text"
                       required
                       name="fix_date"
                       id="fix_date"
                       readonly>
                <div v-if="hasError('fix_date')" class="invalid-feedback">
                    {{ errorText('fix_date') }}
                </div>
            </div>
            <div class="form-group px-0">
                <label class="font-weight-bold">Ответственные подразделения:</label>
                <div v-for="department in departments">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox"
                               :disabled="loading"
                               class="custom-control-input"
                               :id="getIdentifier('department', department.id)"
                               name="responsible_departments[]"
                               :value="department.id"
                               v-model="form_data.responsible_departments">
                        <label class="custom-control-label font-weight-normal" :for="getIdentifier('department', department.id)">
                            {{ department.name }}
                        </label>
                    </div>
                </div>
            </div>
            <div>
                <label class="font-weight-bold">Мероприятия:</label>
                <div v-if="form_data.measures.length > 0" class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Существующие мероприятия будут удалены, а новые - добавлены, только после сохранения события.
                </div>
                <div class="row mb-2">
                    <div class="col-12 text-center">
                        Добавить мероприятие
                        <br>
                        <button type="button"
                                :disabled="loading"
                                data-toggle="modal"
                                data-target="#addMeasureModal"
                                title="Добавить событие"
                                class="btn btn-link">
                            <i class="fas fa-plus-circle" style="font-size: xx-large;"></i>
                        </button>
                    </div>
                </div>
                <div class="modal fade"
                     id="addMeasureModal"
                     tabindex="-1"
                     role="dialog"
                     aria-labelledby="addMeasureModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header p-0">
                                <h5 class="modal-title pt-3 pl-3" id="addMeasureModalLabel">Добавить мероприятие</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="measure_form">
                                    <textarea class="form-control"
                                              v-model="measure_text"
                                              id="measure_text"
                                              name="measure_text"
                                              placeholder="Укажите текст мероприятия"></textarea>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                                <button type="button"
                                        :disabled="measure_text.length === 0"
                                        class="btn btn-success"
                                        @click="addMeasure()">Добавить</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-for="(measure, index) in added_measures" class="row pb-3">
                    <div class="col-12">
                        <div class="card border-dark">
                            <div class="card-header border-dark">
                                Новое мероприятие
                                <button type="button"
                                        :disabled="loading"
                                        class="close"
                                        aria-label="Close"
                                        @click="removeMeasure(index, false)">
                                    <i class="fas fa-times fa-xs"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                {{ measure.text }}
                                <input type="hidden" :name="getIdentifier('measure_', index)" :value="measure.text"/>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="measures_count" :value="added_measures.length" />
                <div v-for="measure in form_data.measures" class="row pb-2">
                    <div class="col-12">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-light">
                                Мероприятие №{{ measure.index }}
                                <button v-if="measureRemoved(measure.id)"
                                        type="button"
                                        class="close"
                                        :disabled="loading"
                                        aria-label="Close"
                                        @click="restoreMeasure(measure.id)">
                                    <i class="fas fa-redo fa-xs text-light"></i>
                                </button>
                                <button v-else
                                        type="button"
                                        class="close"
                                        :disabled="loading"
                                        aria-label="Close"
                                        @click="removeMeasure(measure.id, true)">
                                    <i class="fas fa-times fa-xs text-light"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                {{ measure.text }}
                            </div>
                            <div class="card-footer border-primary text-muted">
                                {{ measure.created_by }} ({{ measure.created_at }})
                            </div>
                        </div>
                    </div>
                </div>
                <input v-for="(id, index) in removed_measures" type="hidden" :name="getIdentifier('removed_measure_', index)" :value="id">
                <input type="hidden" name="removed_measures_count" :value="removed_measures.length">
            </div>
            <div class="form-group mt-2 row">
                <div class="col">
                    <button type="submit" class="btn btn-success btn-block" :disabled="loading" @click.prevent="submitForm()">
                        Сохранить
                    </button>
                </div>
                <div class="col">
                    <a :href="events_url" class="btn btn-info btn-block">
                        К событиям
                    </a>
                </div>
            </div>
        </div>
    </div>
    </form>
</template>

<script>
    // Импортируем нужные примеси
    import Helpers from './mixins/Helpers';
    import Validation from './mixins/Validation';
    import EventForm from './mixins/EventForm';
    // Компонент формы редактирования события
    export default {
        name: "EventEditFormComponent",
        mixins:[
            Helpers,
            Validation,
            EventForm
        ],
        // Свойства
        props: {
            // Событие
            event: Object,
        },
        // Данные
        data: function () {
            return {
                // Флаг того, что событие связано с рейсом
                flight_connection: this.event.flight_id !== null,
                // Данные формы
                form_data: {
                    // Рейс
                    flight_id: this.emptyOrValue(this.event.flight_id),
                    // Где произошло
                    airport: this.emptyOrValue(this.event.airport),
                    // Инициатор
                    initiator: this.emptyOrValue(this.event.initiator),
                    // К чему относится
                    relation_id: this.emptyOrValue(this.event.relation_id),
                    // Подразделение
                    department_id: this.emptyOrValue(this.event.department_id),
                    // Категория
                    category_id: this.emptyOrValue(this.event.category_id),
                    // Статус одобрения
                    approved: "",
                    // Тип
                    type_id: this.emptyOrValue(this.event.type_id),
                    // Вложения
                    attachments: this.event.attachments,
                    // Сообщение
                    message: this.event.message,
                    // Комментарий
                    commentary: this.emptyOrValue(this.event.commentary),
                    // Статус
                    status: this.event.status,
                    // Выявленная причина
                    reason: this.emptyOrValue(this.event.reason),
                    // Принятое решение
                    decision: this.emptyOrValue(this.event.decision),
                    // Ответственные подразделения
                    responsible_departments: [],
                    // Мероприятия
                    measures: [],
                },
                // Удаленные вложения
                removed_attachments: [],
                // Добавленные мероприятия
                added_measures: [],
                // Текст добавляемого мероприятия
                measure_text: "",
                // Удаленные мероприятия
                removed_measures: []
            };
        },
        // Методы
        methods: {
            // Проверка того, что мероприятие удалено
            measureRemoved: function(id) {
                return _.includes(this.removed_measures, id);
            },
            // Добавление мероприятия
            addMeasure: function() {
                this.added_measures.push({
                    text: this.measure_text
                });
                this.measure_text = "";
                $("#addMeasureModal").modal('hide');
            },
            // Удаление мероприятия
            removeMeasure: function(index, existing) {
                if(existing) {
                    this.removed_measures.push(index);
                } else {
                    this.added_measures.splice(index, 1);
                }
            },
            // Восстановление мероприятия
            restoreMeasure: function(id) {
                var index = _.indexOf(this.removed_measures, id);
                this.removed_measures.splice(index, 1);
            },
            // Проверка того, что вложение удалено
            attachmentRemoved: function(id) {
                return _.includes(this.removed_attachments, id);
            },
            // Удаление вложения
            removeAttachment: function(id) {
                this.removed_attachments.push(id);
            },
            // Восстановление вложения
            restoreAttachment: function(id) {
                var index = _.indexOf(this.removed_attachments, id);
                this.removed_attachments.splice(index, 1);
            },
        },
        // Хук монтирования компонента
        mounted() {
            // Инициализируем дату
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
            }).data('datepicker').selectDate(new Date(this.event.date));
            // Если событие связано с рейсом
            if(this.event.flight_id !== null) {
                // Добавляем рейс к рейсам
                this.flights.push(this.event.flight);
                // Добавляем аэропорты рейса к аэропортам
                this.airports.push(this.event.flight.departure_airport, this.event.flight.arrival_airport);
            }
            // Если указано подразделение - загружаем категории
            if(this.form_data.department_id !== null) {
                this.updateCategories();
            }
            // Проставляем статус одобрения события
            if(this.event.approved !== null) {
                this.form_data.approved = this.event.approved ? 1 : 0;
            }
            // Инициализируем дату устранения события
            var fix_date = $("#fix_date").datepicker({
                language: "ru",
                minDate: new Date(this.event.date),
                maxDate: new Date(),
                autoClose: true,
                position: "bottom left",
                todayButton: new Date(),
                clearButton: true,
                onSelect: (formattedDate, date, inst) => {
                    this.unsetErrorField('fix_date');
                }
            });
            // Устанавливаем дату, если она выбрана
            if(this.event.fix_date !== null) {
                fix_date.data('datepicker').selectDate(new Date(this.event.fix_date));
            }
            // Заполняем ответственные подразделения
            _.forEach(this.event.responsible_departments, department => {
                this.form_data.responsible_departments.push(department.id);
            });
            // Заполняем мероприятия
            var measureIndex = 1;
            _.forEach(this.event.measures, measure => {
                var date = moment(measure.created_at);
                this.form_data.measures.push({
                    id: measure.id,
                    index: measureIndex,
                    text: measure.text,
                    created_by: measure.user_created_by.name,
                    created_at: date.format('DD.MM.YYYY HH:mm.ss')
                });
                measureIndex++;
            });
        }
    }
</script>
