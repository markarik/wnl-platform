<template>
	<div ref="overviewContainer" class="scrollable-main-container">
		<!-- Dashboard news -->
		<wnl-dashboard-news />

		<div class="welcome-container">
			<div class="welcome">
				{{$t('dashboard.welcome', {currentUserName})}} <wnl-emoji name="wave" />
			</div>
			<div v-if="currentUserSubscriptionActive" class="access-display">
				<div>
					Twój dostęp do kursu jest aktywny do:&nbsp;
				</div>
				<div class="access-display__date">
					{{userFriendlySubscriptionDate}}
				</div>
			</div>
		</div>
		<!-- Next lesson -->
		<div class="overview-progress box">
			<wnl-next-lesson @userEvent="trackUserEvent" />
			<wnl-your-progress />
		</div>

		<div class="active-users">
			<wnl-active-users />
		</div>

		<div class="news-heading metadata">
			{{$t('dashboard.news.heading')}}
		</div>
		<wnl-stream-feed />
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.welcome-container
		display: flex
		align-items: center
		justify-content: space-between
		.welcome
			font-size: $font-size-minus-1
			font-weight: bold
			margin-bottom: $margin-base
			text-transform: uppercase
		.access-display
			display: flex
			font-size: $font-size-minus-1
			align-items: center
			margin-bottom: $margin-base
			.access-display__date
				font-weight: bold

	.news-heading
		border-bottom: $border-light-gray
		margin: $margin-big 0 $margin-small

</style>

<script>
import { mapGetters } from 'vuex';

import WnlActiveUsers from 'js/components/course/dashboard/ActiveUsers';
import WnlDashboardNews from 'js/components/course/dashboard/DashboardNews';
import WnlNextLesson from 'js/components/course/dashboard/NextLesson';
import WnlStreamFeed from 'js/components/notifications/feeds/stream/StreamFeed';
import WnlYourProgress from 'js/components/course/dashboard/YourProgress';
import moment from 'moment';
import context from 'js/consts/events_map/context.json';

export default {
	name: 'Overview',
	components: {
		WnlActiveUsers,
		WnlDashboardNews,
		WnlNextLesson,
		WnlStreamFeed,
		WnlYourProgress,
	},
	props: ['courseId'],
	computed: {
		...mapGetters('progress', [
			'isLessonComplete',
		]),
		...mapGetters([
			'currentUserName',
		]),
		...mapGetters([
			'currentUserSubscriptionDates',
			'currentUserSubscriptionActive',
			'currentUserHasLatestProduct',
		]),
		userFriendlySubscriptionDate() {
			return moment(this.currentUserSubscriptionDates.max*1000).locale('pl').format('LL');
		},
	},
	methods: {
		trackUserEvent(payload) {
			this.$trackUserEvent({
				...payload,
				context: context.dashboard.value,
			});
		}
	},
};
</script>
