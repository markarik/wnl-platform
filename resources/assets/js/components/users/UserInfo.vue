<template>
    <div class="user-info">
        <h1>{{ label }}</h1>
        <h3>{{ info }}</h3>
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
