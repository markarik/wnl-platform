<template>
    <div class="user-info">
        <h3>{{ label }}</h3>
        <h4>{{ info }}</h4>
        <hr>
    </div>
</template>

<script>
export default {
    name: 'UserInfo',
    props: ['label', 'info'],
}
</script>

<style lang="sass">
    @import 'resources/assets/sass/variables'

    .user-info
        border: $border-light-gray
</style>
