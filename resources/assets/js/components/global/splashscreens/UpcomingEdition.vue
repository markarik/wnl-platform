<template>
    <div class="splash-screen">
        <img class="splash-screen-image" :src="countdownImageUrl" alt="Odliczamy dni do kursu">
        <div class="splash-screen-countdown">
            <p class="title is-4">Dostęp do kursu uzyskasz już {{startDate}}!</p>
            <p class="info"></p>
            <p class="info">
                Twoje zamówienia znajdziesz w zakładce - <router-link :to="{name: 'my-orders'}">KONTO > Twoje zamówienia</router-link>.
            </p>
        </div>
    </div>
</template>

<style lang="sass" scoped>
    @import 'resources/assets/sass/variables'

    .splash-screen
        align-items: center
        display: flex
        flex: 1 0 auto
        flex-direction: column
        height: 100%
        justify-content: center
        min-height: 100%
        width: 100%

    .splash-screen-image
        max-width: 400px
        padding: 0 20px

    .splash-screen-countdown
        font-size: $font-size-plus-7
        font-weight: $font-weight-black
        line-height: $line-height-plus
        text-align: center

        .info
            font-size: $font-size-base
            font-weight: $font-weight-regular
            line-height: $line-height-base
            margin: $margin-base

</style>

<script>
import moment from 'moment';
import { mapGetters } from 'vuex';
import { getImageUrl } from 'js/utils/env';

export default {
	computed: {
		...mapGetters(['currentUserSubscriptionDates', 'currentUserAccountSuspended']),
		countdownImageUrl() {
			return getImageUrl('countdown.png');
		},
		startDate() {
			return moment(new Date(this.currentUserSubscriptionDates.min * 1000)).format('LL');
		},
	}
};
</script>
