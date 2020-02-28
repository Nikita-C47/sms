<template>
    <input type="hidden" name="_token" :value="token">
</template>

<script>
    export default {
        data: function() {
            return {
                token: $('meta[name="csrf-token"]').attr('content')
            };
        },
        name: "CsrfFieldComponent"
    }
</script>