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
			<span class="news-heading-description">
				{{$t('dashboard.news.description')}}
			</span>
		</div>
		<div class="current-view-controls">
			<a
				v-for="(panel, index) in panels"
				:key="index"
				class="panel-toggle"
				:class="{'is-active': overviewView === panel.slug}"
				@click="changeOverviewView(panel.slug)"
			>
				{{panel.name}}
				<span class="icon is-small">
					<i class="fa" :class="panel.icon"></i>
				</span>
			</a>
		</div>
		<wnl-stream-feed v-show="overviewView === 'stream'" />
		<wnl-qna
			v-show="overviewView === 'qna'"
			:sorting-enabled="true"
			:numbers-disabled="true"
			:hide-title="true"
			class="wnl-overview-qna"
		/>
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

		.news-heading-description
			color: $color-background-gray
			display: block
			font-weight: $font-weight-regular
			text-transform: none

	.current-view-controls
		align-items: center
		display: flex
		flex-wrap: wrap
		margin-bottom: $margin-base

		.panel-toggle
			margin-top: $margin-small

	.wnl-overview-qna
		margin: -$margin-base 0 $margin-huge

</style>

<script>
import { mapActions, mapGetters } from 'vuex';

import ActiveUsers from 'js/components/course/dashboard/ActiveUsers';
import DashboardNews from 'js/components/course/dashboard/DashboardNews';
import NextLesson from 'js/components/course/dashboard/NextLesson';
import Qna from 'js/components/qna/Qna';
import StreamFeed from 'js/components/notifications/feeds/stream/StreamFeed';
import YourProgress from 'js/components/course/dashboard/YourProgress';
import moment from 'moment';
import { getUrl } from 'js/utils/env';
import context from 'js/consts/events_map/context.json';

export default {
	name: 'Overview',
	components: {
		'wnl-active-users': ActiveUsers,
		'wnl-dashboard-news': DashboardNews,
		'wnl-next-lesson': NextLesson,
		'wnl-qna': Qna,
		'wnl-stream-feed': StreamFeed,
		'wnl-your-progress': YourProgress,
	},
	props: ['courseId'],
	computed: {
		...mapGetters('progress', [
			'isLessonComplete',
		]),
		...mapGetters([
			'currentUserName',
			'overviewView',
		]),
		...mapGetters([
			'currentUserSubscriptionDates',
			'currentUserSubscriptionActive',
			'currentUserHasLatestProduct',
		]),
		panels() {
			return [
				{
					name: this.$t('dashboard.news.stream'),
					slug: 'stream',
					icon: 'fa-commenting-o'
				},
				{
					name: this.$t('dashboard.news.qna'),
					slug: 'qna',
					icon: 'fa-question-circle-o',
				},
			];
		},
		userFriendlySubscriptionDate() {
			return moment(this.currentUserSubscriptionDates.max*1000).locale('pl').format('LL');
		},
		signUpLink() {
			return getUrl('payment/account');
		},
	},
	methods: {
		...mapActions(['changeOverviewView']),
		...mapActions('qna', ['fetchLatestQuestions']),
		trackUserEvent(payload) {
			this.$trackUserEvent({
				...payload,
				context: context.dashboard.value,
			});
		}
	},
	mounted() {
		this.fetchLatestQuestions();
	}
};
</script>
