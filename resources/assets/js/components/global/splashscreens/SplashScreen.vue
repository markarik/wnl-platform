<template>
	<div class="splash-screen__container">
		<div class="splash-screen__content">
					<wnl-upcoming-edition v-if="$upcomingEditionParticipant.isAllowed('access')"/>
					<wnl-no-access v-else-if="currentUserAccountSuspended"/>
					<wnl-order-not-paid v-else />
		</div>
		<footer class="splash-screen__footer text-dimmed">
			<p class="splash-screen__footer__text">
				W razie problemów napisz do nas na Messengerze lub wyślij maila na adres: <a href="mailto:info@wiecejnizlek.pl">info@wiecejnizlek.pl</a>
			</p>
		</footer>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.splash-screen__container
		display: flex
		flex-direction: column
		flex: 1 0 auto
		height: 100%
		width: 100%

	.splash-screen__content
		flex: 1 0 auto

	.splash-screen__footer
		border-top: $border-light-gray
		padding: $margin-big

		&__text
			max-width: 320px
</style>

<script>
import { mapGetters } from 'vuex';
import upcomingEditionParticipant from 'js/perimeters/upcomingEditionParticipant';
import WnlNoAccess from 'js/components/global/splashscreens/NoAccess';
import WnlUpcomingEdition from 'js/components/global/splashscreens/UpcomingEdition';
import WnlOrderNotPaid from 'js/components/global/splashscreens/OrderNotPaid';

export default {
	components: { WnlNoAccess, WnlUpcomingEdition, WnlOrderNotPaid },
	perimeters: [upcomingEditionParticipant],
	computed: {
		...mapGetters(['currentUserAccountSuspended']),
	}
};
</script>
