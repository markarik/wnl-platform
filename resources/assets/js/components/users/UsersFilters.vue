<template>
    <div class="wnl-public-chat">
        <div class="chat-title">
            <p>Filtry</p>
        </div>
        <a class="wnl-chat-close">
            <span v-if="canShowCloseIconInChat" class="icon wnl-chat-close" @click="toggleChat">
                <i class="fa fa-chevron-right"></i>
                <span>Ukryj filtry</span>
            </span>
        </a>
        <div class="filters">
            <div class="filterByLocation">
                <label for="filterByLocation">Miasto</label><br>
                <input type="text" name="filterByLocation" v-model="filterByLocation" @keyup.enter="emitFilterByLocation">
            </div>
            <hr>
            <div class="filterByHelp">
                <label for="filterByHelp">W czym możemy Ci pomóc?</label><br>
                <input type="text" name="filterByHelp" v-model="filterByHelp" @keyup.enter="emitFilterByHelp">
            </div>
            <hr>
            <div class="filterBy">

            </div>
        </div>
    </div>
</template>

<style lang="sass">
    @import 'resources/assets/sass/variables'

    .wnl-public-chat
        display: flex
        flex: 1
        flex-direction: column
        justify-content: flex-start
        padding: $margin-base
        position: relative

        .wnl-chat-close
            color: $color-ocean-blue
            cursor: pointer
            display: flex
            flex-direction: column
            position: absolute
            right: $margin-base
            top: $margin-base

            span
                font-size: $font-size-minus-4
                text-transform: uppercase
                white-space: nowrap


    .metadata
        margin: $margin-base 0 0 $margin-base

    .chat-title
        color: $color-gray-dimmed
        font-size: $font-size-minus-2
        text-transform: uppercase

</style>

<script>
import { mapActions, mapGetters } from 'vuex'
import UserInfo from 'js/components/users/UserInfo'

export default {
    name: 'UserFilters',
    data() {
        return {
            filterByLocation: '',
            filterByHelp: ''
        }
    },
    computed: {
        ...mapGetters(['canShowCloseIconInChat']),
    },
    methods: {
        ...mapActions(['toggleChat']),
        emitFilterByLocation() {
            return this.$emit('locationFilterLoaded', this.filterByLocation)
        },
        emitFilterByHelp() {
            return this.$emit('helpFilterLoaded', this.filterByHelp)
        },
    },
}
</script>
