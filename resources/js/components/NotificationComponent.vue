<template>
    <div class="d-none"></div>
</template>

<script>
    export default {
        name: "NotificationComponent",
        props: {
            type: String,
            message: String
        },
        data: function() {
            return {
                icons: {
                    success: 'fas fa-check',
                    danger: 'fas fa-times',
                    warning: 'fas fa-exclamation',
                    primary: 'fas fa-bell',
                    secondary: 'fas fa-bullhorn'
                }
            }
        },
        mounted() {
            var content = {
                message: this.message,
                title: "Внимание!",
                icon: this.icons[this.type],
            };

            $.notify(content,{
                type: this.type,
                placement: {
                    from: 'top',
                    align: 'right'
                },
                delay: 3000,
            });
        }
    }
</script>

<style scoped>

</style>
