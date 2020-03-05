<template>
    <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox"
               :id="identifier"
               :name="identifier"
               v-model="is_checked"
               @change="setCheckboxes()"
               class="custom-control-input">
        <label class="custom-control-label" :for="identifier">
            Все
        </label>
    </div>
</template>

<script>
    export default {
        name: "SelectAllComponent",
        props: {
            name: String,
            checked: Number
        },
        computed: {
            identifier: function () {
                return this.name + '-all';
            },
            selector: function () {
                return "[name='"+this.name+"[]']";
            }
        },
        data: function () {
            return {
                is_checked: null
            }
        },
        methods: {
            setCheckboxes: function () {
                var checkboxes = $(this.selector);
                checkboxes.prop('checked', this.is_checked);
            }
        },
        mounted() {
            this.is_checked = this.checked > 0;

            var checkboxes = $(this.selector);
            if(this.is_checked) {
                checkboxes.prop('checked', true);
            }

            checkboxes.click(() => {
                var select_all = $("#"+this.identifier);

                if(select_all.prop("checked")) {
                    this.is_checked = false;
                    //select_all.prop("checked", false);
                }

            });
        }
    }
</script>
