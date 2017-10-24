<template>
    <div class="wnl-background" :class="[colorClass]">
        <slot></slot>
    </div>

</template>

<style lang="sass" rel="stylesheet/sass">

    $background-colors-list: #9ce5d6, #9eedc0, #90bedd, #d3a7e5, #a8c4e0, #a6dbd1, #92dbb1, #72b1db, #d1a2e5, #89bbed, #c9b566, #cca27e, #d6948d, #d1a764, #bf8d6d, #d6796f

    @for $i from 1 to 16
        .wnl-background-color-#{$i}
            background-color: nth($background-colors-list, $i)

    .wnl-background
        height: 22vh
        width: 100%
</style>

<script>
import {getInitials} from 'js/utils/strings'

export default {
    name: 'UserBackground',
    props: ['fullName'],
    computed: {
        colorClass() {
            let colorPosition = (this.initials.charCodeAt(0) - 65) % 15 + 1
            return `wnl-background-color-${colorPosition}`
        },
        isCurrentUser() {
            return _.isEmpty(this.fullName)
        },
        usernameToUse() {
            return this.fullName
        },
        initials() {
            return getInitials(this.usernameToUse)
        },
    },
}
</script>
