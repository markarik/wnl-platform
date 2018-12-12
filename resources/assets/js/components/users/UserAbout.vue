<template>
    <div class="wnl-user-about">
        <div class="about-title">
            <p>{{ $t('user.userProfile.about') }}</p>
        </div>
        <div class="user-infos">
            <wnl-user-info
                :label="$t('user.userAboutLabels.specialization')"
                :info="profile.specialization"
            ></wnl-user-info>
            <wnl-user-info
                :label="$t('user.userAboutLabels.university')"
                :info="profile.university"
            ></wnl-user-info>
            <wnl-user-info
                :label="$t('user.userAboutLabels.city')"
                :info="profile.city"
            ></wnl-user-info>
            <wnl-user-info
                :label="$t('user.userAboutLabels.learning_location')"
                :info="profile.learning_location"
            ></wnl-user-info>
            <wnl-user-info
                :label="$t('user.userAboutLabels.interests')"
                :info="profile.interests"
            ></wnl-user-info>
            <wnl-user-info
                :label="$t('user.userAboutLabels.about')"
                :info="profile.about"
            ></wnl-user-info>
        </div>
    </div>

</template>

<style lang="sass" rel="stylesheet/sass" scoped>
    @import 'resources/assets/sass/variables'

    .wnl-user-about
        display: flex
        flex: 1
        flex-direction: column
        justify-content: flex-start
        overflow: auto
        padding: $margin-base
        position: relative

    .metadata
        margin: $margin-base 0 0 $margin-base

    .about-title
        color: $color-gray-dimmed
        font-size: $font-size-minus-2
        text-transform: uppercase
        margin-bottom: $margin-big

    .user-infos
        width: 100%

</style>

<script>
import { mapActions, mapGetters } from 'vuex';
import UserInfo from 'js/components/users/UserInfo';

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
};
</script>
