export default {
    data: function() {
        return {
            validation_errors: {}
        };
    },
    methods: {
        hasError: function (fieldName) {
            return _.hasIn(this.validation_errors, fieldName);
        },
        errorText: function (fieldName, multiple = false) {
            if(this.hasError(fieldName)) {
                return multiple ? this.validation_errors[fieldName] : this.validation_errors[fieldName][0];
            }
            return "";
        },
        unsetErrorField: function (fieldName) {
            if(this.hasError(fieldName)) {
                this.$delete(this.validation_errors, fieldName);
            }
        },
        fieldClasses: function(fieldName, defaultClasses = 'form-control') {
            var classes = [defaultClasses];

            if(defaultClasses === 'form-control') {
                classes.push('input-solid');
            }

            if(this.hasError(fieldName)) {
                classes.push('is-invalid');
            }

            return classes;
        },
        scrollToFirstError: function() {
            var errorsChecker = setInterval(function () {
                var firstError = $(".invalid-feedback:first");
                if(firstError.length === 1) {
                    $('html, body').animate({
                        scrollTop: firstError.parents('.form-group:first').offset().top - 70
                    }, 1000);
                    clearInterval(errorsChecker);
                }
            });
        },
        normalizeErrors: function(errorsObject) {
            _.forEach(errorsObject, function (value, key) {
                var splittedKey = _.split(key, '.');
                if(splittedKey.length > 1) {
                    if(_.hasIn(errorsObject, splittedKey[0])) {
                        errorsObject[splittedKey[0]].push(value);
                    } else {
                        errorsObject[splittedKey[0]] = [value];
                    }
                }
            });
            return errorsObject;
        },
    }
}
