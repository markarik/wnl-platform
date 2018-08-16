<template>
	<div class="scrollable-main-container" ref="overviewContainer">
		<div class="notification is-info">
			<span class="strong">Cześć!</span> W środę, 22 sierpnia, od godziny 10:00, kontynuujemy próby przełączenia się na nową infrastrukturę. 🚀 Ponownie prosimy o wyrozumiałość i zaplanowanie nauki od godziny 13:00. Dziękujemy! ❤️ Więcej informacji <a href="https://platforma.wiecejnizlek.pl/app/help/new?qna_question=888&notification=9ab1a570-7c2f-4984-a427-ab97d1750cf9&noScroll=true" target="_blank">znajdziesz tutaj</a>.
		</div>

		<!-- Dashboard news -->
		<wnl-dashboard-news/>

		<div class="welcome">
			{{ $t('dashboard.welcome', {currentUserName}) }} <wnl-emoji name="wave"/>
		</div>

		<!-- Next lesson -->
		<div class="overview-progress box">
			<wnl-next-lesson/>
			<wnl-your-progress/>
		</div>

		<div class="active-users">
			<wnl-active-users/>
		</div>

		<div class="news-heading metadata">
			{{ $t('dashboard.news.heading') }}
			<span class="news-heading-description">
				{{ $t('dashboard.news.description') }}
			</span>
		</div>
		<div class="current-view-controls">
			<a v-for="panel, index in panels" class="panel-toggle"
				:class="{'is-active': overviewView === panel.slug}"
				:key="index"
				@click="changeOverviewView(panel.slug)"
			>
				{{panel.name}}
				<span class="icon is-small">
					<i class="fa" :class="panel.icon"></i>
				</span>
			</a>
		</div>
		<wnl-stream-feed v-show="overviewView === 'stream'"/>
		<wnl-qna :sortingEnabled="true" :numbersDisabled="true" v-show="overviewView === 'qna'" :hideTitle="true" class="wnl-overview-qna"/>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.welcome
		font-size: $font-size-minus-1
		font-weight: bold
		margin-bottom: $margin-base
		text-transform: uppercase

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
	import emoji from 'node-emoji'
	import { mapGetters, mapActions } from 'vuex'

	import ActiveUsers from 'js/components/course/dashboard/ActiveUsers'
	import DashboardNews from 'js/components/course/dashboard/DashboardNews'
	import NextLesson from 'js/components/course/dashboard/NextLesson'
	import Qna from 'js/components/qna/Qna'
	import StreamFeed from 'js/components/notifications/feeds/stream/StreamFeed'
	import YourProgress from 'js/components/course/dashboard/YourProgress'
	import { resource } from 'js/utils/config'

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
				'wasCourseStarted',
			]),
			...mapGetters([
				'currentUserName',
				'overviewView',
			]),
			isBeginning() {
				return !this.wasCourseStarted(this.courseId)
			},
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
				]
			},
		},
		methods: {
			...mapActions(['changeOverviewView']),
			...mapActions('qna', ['fetchLatestQuestions']),
		},
		mounted() {
			this.fetchLatestQuestions()
		}
	}
</script>
