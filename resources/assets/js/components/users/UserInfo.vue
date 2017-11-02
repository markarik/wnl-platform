<template>
    <div class="user-info">
        <span class="label">{{ label }}</span>
        <span class="info">{{ info }}</span>
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
        .label
            text-transform: uppercase
            font-size: $font-size-plus-1
            font-weight: $font-weight-regular
            color: $color-gray-dimmed
        .info

</style>
