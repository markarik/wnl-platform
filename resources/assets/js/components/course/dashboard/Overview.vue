<template>
	<div class="scrollable-main-container" ref="overviewContainer">
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

		<div class="news-heading metadata">{{ $t('dashboard.news.heading') }}</div>
		<div class="current-view-controls">
			<a v-for="panel, index in panels" class="panel-toggle"
				:class="{'is-active': currentView === panel.slug}"
				:key="index"
				@click="currentView = panel.slug"
			>
				{{panel.name}}
				<span class="icon is-small">
					<i class="fa" :class="panel.icon"></i>
				</span>
			</a>
		</div>
		<wnl-stream-feed v-show="currentView === 'stream'"/>
		<wnl-qna v-show="currentView === 'qna'" :title="false" class="wnl-overview-qna"/>
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

	.current-view-controls
		align-items: center
		display: flex
		flex-wrap: wrap
		margin-bottom: $margin-base

		.panel-toggle
			margin-top: $margin-small

	.wnl-overview-qna
		margin: 0 0 $margin-huge
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
	import { getFirstLessonId } from 'js/utils/env'
	import { resource } from 'js/utils/config'

	export default {
		props: ['courseId'],
		data() {
			return {
				currentView: 'stream',
			}
		},
		computed: {
			...mapGetters('progress', [
				'isLessonComplete',
				'wasCourseStarted',
			]),
			...mapGetters([
				'currentUserName',
			]),
			isBeginning() {
				return !this.wasCourseStarted(this.courseId)
			},
			panels() {
				return [
					{
						name: this.$t('dashboard.news.stream'),
						slug: 'stream',
						icon: 'fa-globe'
					},
					{
						name: this.$t('dashboard.news.qna'),
						slug: 'qna',
						icon: 'fa-question-circle-o',
					},
				]
			},
		},
		components: {
			'wnl-active-users': ActiveUsers,
			'wnl-dashboard-news': DashboardNews,
			'wnl-next-lesson': NextLesson,
			'wnl-qna': Qna,
			'wnl-stream-feed': StreamFeed,
			'wnl-your-progress': YourProgress,
		},
		methods: {
			...mapActions('qna', ['fetchLatestQuestions'])
		},
		beforeMount() {
			if (this.isBeginning) {
				this.$router.replace({
					name: resource('lessons'),
					params: {
						lessonId: getFirstLessonId(),
						courseId: this.courseId
					}
				})
			}
		},
		mounted() {
			this.fetchLatestQuestions()
		}
	}
</script>
