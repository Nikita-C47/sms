<template>
    <div class="d-inline">
        <button @click="showModal(id)" class="btn btn-sm btn-danger" title="Удалить">
            <i class="fa fa-times-circle"></i>
        </button>
        <div :id="calculateId(id)" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header p-0">
                        <h5 class="modal-title pt-3 pl-3">Подтверждение удаления</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-wrap">
                            <span class="text-danger">ВНИМАНИЕ!</span>
                            <span>
                                Вы действительно хотите удалить запись &laquo;{{ entity_name }}&raquo;? Это действие необратимо.
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <form method="post" :action="action">
                            <csrf-field></csrf-field>
                            <button type="submit" class="btn btn-danger">Удалить</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    // Компонент модального окна подтверждения удаления
    export default {
        // Свойства
        props: {
            // ID
            id: Number,
            // Название удаляемой записи
            entity_name: String,
            // Адрес отправки формы удаления
            action: String
        },
        name: "ConfirmationModalComponent",
        // Методы
        methods: {
            // Генерирует ID модального окна удаления
            calculateId: function(id) {
                return "deleteConfirmationId"+id;
            },
            // Показывает модальное окно
            showModal: function (id) {
                $("#"+this.calculateId(id)).modal('show');
            }
        }
    }
</script>
