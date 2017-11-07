<template>
    <div class="user-info">
        <span class="label" v-t="label"></span>
        <span class="info">{{ info }}</span>
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
        margin-bottom: $margin-base
        border-bottom: $border-light-gray
        padding-bottom: $margin-base
        .label
            text-transform: uppercase
            font-size: $font-size-plus-1
            font-weight: $font-weight-regular
            color: $color-gray-dimmed

</style>
