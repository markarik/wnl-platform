<template>
    <div class="wnl-public-chat">
        <div class="chat-title">
            <p>Info o uczestniku</p>
        </div>
        <br>
        <!-- <a class="wnl-chat-close">
            <span v-if="!canShowCloseIconInChat" class="icon wnl-chat-close" @click="toggleChat">
                <i class="fa fa-chevron-right"></i>
                <span>Ukryj info</span>
            </span>
        </a> -->
        <div class="user-infos">
            <wnl-user-info :label="`Miasto`" :info="profile.city"></wnl-user-info>
            <wnl-user-info :label="`Uniwersytet`" :info="profile.university"></wnl-user-info>
            <wnl-user-info :label="`Wymarzona specjalizacja`" :info="profile.specialization"></wnl-user-info>
            <wnl-user-info :label="`Zainteresowania`" :info="profile.interests"></wnl-user-info>
            <wnl-user-info :label="`O mnie`" :info="profile.about"></wnl-user-info>
            <wnl-user-info :label="`Gdzie zwykle się uczę?`" :info="profile.learning_location"></wnl-user-info>
        </div>
    </div>

</template>

<style lang="sass" rel="stylesheet/sass" scoped>
    @import 'resources/assets/sass/variables'

    .wnl-public-chat
        display: flex
        flex: 1
        flex-direction: column
        justify-content: flex-start
        padding: $margin-base
        position: relative

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
    name: 'UserAbout',
    props: ['profile'],
    components: {
        'wnl-user-info': UserInfo,
    },
    computed: {
        ...mapGetters(['canShowCloseIconInChat']),
    },
    methods: {
        ...mapActions(['toggleChat']),
    },
}
</script>
