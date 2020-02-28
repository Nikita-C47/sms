export default {
    methods: {
        changeFileInput: function (event) {
            this.unsetErrorField('attachments');
            var element = event.target;
            var files = element.files;
            var placeholder = $("label[for='"+element.id+"']");

            if(files.length === 0) {
                var defaultPlaceholder = "Выберите файлы";
                placeholder.text(defaultPlaceholder);
            } else {
                if(files.length === 1) {
                    placeholder.text(files[0].name);
                } else {
                    placeholder.text('Выбрано файлов: '+files.length);
                }
            }
        },
        emptyOrValue: function(value) {
            return value === null ? "" : value;
        },
        getIdentifier: function(prefix, id) {
            return prefix + id;
        },
    }
}
