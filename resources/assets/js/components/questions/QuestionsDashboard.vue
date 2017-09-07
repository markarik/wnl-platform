<template>
	<div class="wnl-app-layout">
		<wnl-questions-navigation/>
		<div class="wnl-middle wnl-app-layout-main">
			<div class="scrollable-main-container">
				<div class="questions-header">
					<div class="questions-breadcrumbs">
						<div class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-check-square-o"></i></span>
						</div>
						<div class="breadcrumb" @click="$router.push({name: 'questions-dashboard'})">
							<span class="icon is-small"><i class="fa fa-angle-right"></i></span>
							<span>{{$t('questions.nav.dashboard')}}</span>
						</div>
						<div v-if="id" class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-angle-right"></i></span>
							<span>#{{id}}</span>
						</div>
					</div>
					<a v-if="!isLargeDesktop" class="toggle-notifications" @click="toggleChat">
						<span>{{$t('questions.dashboard.notifications.toggle')}}</span>
						<span class="icon is-small">
							<i class="fa fa-commenting-o"></i>
						</span>
					</a>
				</div>
				<div v-if="!id">
					<div class="questions-dashboard-plan">
						<div class="questions-dashboard-heading">
							<span class="icon is-small"><i class="fa fa-calendar"></i></span>
							{{$t('questions.dashboard.plan.heading')}}
						</div>
						<div v-if="plan === null" class="margin vertical">
							<wnl-text-loader/>
						</div>
						<wnl-questions-plan-progress v-else-if="hasPlan" :allowChange="false" :plan="plan"/>
						<div class="questions-plan-create" v-else>
							<p class="questions-plan-create-heading">
								{{$t('questions.dashboard.plan.create.heading')}}
							<p>
							<p class="questions-plan-create-tip">
								{{$t('questions.dashboard.plan.create.tip')}}
							</p>
							<p class="margin vertical has-text-centered">
								<router-link class="button is-primary is-outlined" :to="{name: 'questions-planner'}">
									{{$t('questions.dashboard.plan.create.cta')}}
								</router-link>
							</p>
						</div>
					</div>
					<div v-if="stats === null">
						{{$t('questions.dashboard.stats.error')}}
					</div>
					<div v-else-if="!hasStats" class="margin vertical">
						<wnl-text-loader/>
					</div>
					<div v-else>
						<!-- All questions -->
						<div class="questions-dashboard-heading">
							<span class="icon is-small"><i class="fa fa-bar-chart"></i></span>
							{{$t('questions.dashboard.stats.heading')}}
						</div>
						<div class="questions-dashboard-subheading">
							<span class="icon is-small"><i class="fa fa-tasks"></i></span>
							{{$t('questions.dashboard.stats.scores')}}
						</div>
						<div class="questions-stats margin bottom">
							<div v-for="stats, index in parseStats(stats)"
								class="stats-item stats-resolved"
								:class="{'is-first': index === 0}"
							>
								<span class="stats-title">{{stats.title}}</span>
								<div class="progress-bar">
									<progress class="progress"
										:value="stats.progress"
										:max="stats.total"/>
									<span class="progress-number">{{stats.progressNumber}}</span>
									<div class="score" :class="scoreClass(stats.score)">{{stats.score}}%</div>
								</div>
							</div>
						</div>

						<!-- Mock exam -->
						<div v-if="stats.mock_exam">
							<div class="questions-dashboard-subheading margin top">
								<span class="icon is-small"><i class="fa fa-tachometer"></i></span>
								{{$t('questions.dashboard.stats.mockExam')}}
							</div>
							<div class="questions-stats stats-exam">
								<div v-for="stats, index in parseStats(stats.mock_exam)"
									class="stats-item stats-exam"
									:class="{'is-first': index === 0}"
								>
									<span class="stats-title">{{stats.title}}</span>
									<span class="progress-number" :class="scoreClass(stats.scoreTotal)">{{stats.scoreNumber}}</span>
									<div class="score" :class="scoreClass(stats.scoreTotal)">{{stats.scoreTotal}}%</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<router-view v-else :id="id"/>
			</div>
		</div>
		<wnl-sidenav-slot
			:isDetached="!isChatMounted"
			:isVisible="isLargeDesktop || isChatVisible"
			:hasChat="true"
		>
			<div class="questions-feed-container">
				<div class="questions-feed-heading" :class="{'detached': !isChatMounted}">
					<div>
						<span class="icon is-small"><i class="fa fa-commenting-o"></i></span>
						{{$t('questions.dashboard.notifications.heading')}}
					</div>
					<div v-if="!isChatMounted">
						<span class="metadata">{{$t('questions.dashboard.notifications.close')}}</span>
						<span class="icon is-small" @click="toggleChat">
							<i class="fa fa-close"></i>
						</span>
					</div>
				</div>
				<wnl-questions-feed/>
			</div>
		</wnl-sidenav-slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.wnl-middle
		max-width: 100%
		width: 100%

	.questions-header
		+flex-space-between()

	.questions-breadcrumbs
		align-items: center
		color: $color-gray-dimmed
		font-size: $font-size-minus-1
		display: flex
		margin-right: $margin-base

		.breadcrumb
			max-width: 200px
			overflow-x: hidden
			text-overflow: ellipsis
			white-space: nowrap

	.toggle-notifications
		font-size: $font-size-minus-1

	.questions-dashboard-heading
		border-bottom: $border-light-gray
		font-weight: $font-weight-bold
		letter-spacing: 1px
		margin-top: $margin-base
		text-align: center
		text-transform: uppercase

		.icon
			color: $color-background-gray
			margin-right: $margin-small

	.questions-plan-create
		margin-bottom: $margin-big

	.questions-plan-create-heading
		font-size: $font-size-plus-1
		margin: $margin-medium 0 $margin-small
		text-align: center
		width: 100%

	.questions-plan-create-tip
		font-size: $font-size-minus-1

	.questions-plan-create-tip
		text-align: center
		width: 100%

	.questions-dashboard-subheading
		font-size: $font-size-minus-1
		margin: $margin-medium 0
		text-align: center
		text-transform: uppercase

		.icon
			color: $color-background-gray
			margin-right: $margin-small

	.questions-plan-progress-container
		margin: $margin-medium 0 $margin-huge

	.questions-stats
		.stats-item
			+flex-space-between()
			flex-wrap: wrap
			font-size: $font-size-minus-1
			margin-bottom: $margin-small

			&.stats-exam
				flex-wrap: nowrap
				justify-content: center

				.progress-number
					width: 50px

			&.is-first
				font-size: $font-size-base
				font-weight: $font-weight-bold

				.progress
					height: 1rem

			.stats-title
				margin-right: $margin-medium
				width: 150px

			.progress-bar
				+flex-space-between()
				flex: 1 auto

			.progress
				flex: 1 auto
				height: 4px
				margin-bottom: 0
				margin-right: $margin-medium
				min-width: 120px

			.progress-number
				width: 100px
				text-align: center

			.score
				text-align: center
				width: 60px

			.score,
			.progress-number
				&.is-danger
					color: $color-red

				&.is-success
					color: $color-green

	.questions-feed-container
		width: 100%
		overflow-y: auto

	.questions-feed-heading
		border-bottom: $border-light-gray
		font-size: $font-size-minus-1
		letter-spacing: 1px
		margin-top: $margin-base
		padding-bottom: $margin-small
		text-align: center
		text-transform: uppercase

		&.detached
			+flex-space-between()
			padding: 0 $margin-base $margin-small

		.icon
			color: $color-background-gray
			margin-right: $margin-small
</style>

<script>
	import {isEmpty} from 'lodash'
	import {mapActions, mapGetters} from 'vuex'

	import QuestionsFeed from 'js/components/notifications/feeds/questions/QuestionsFeed'
	import QuestionsNavigation from 'js/components/questions/QuestionsNavigation'
	import QuestionsPlanProgress from 'js/components/questions/QuestionsPlanProgress'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import {getApiUrl} from 'js/utils/env'

	export default {
		name: 'QuestionsDashboard',
		components: {
			'wnl-questions-feed': QuestionsFeed,
			'wnl-questions-navigation': QuestionsNavigation,
			'wnl-questions-plan-progress': QuestionsPlanProgress,
			'wnl-sidenav-slot': SidenavSlot,
		},
		props: {
			id: {
				default: 0,
				type: Number|String,
			}
		},
		data() {
			return {
				plan: null,
				planRoute: {},
				stats: {},
			}
		},
		computed: {
			...mapGetters(['currentUserId','isChatMounted', 'isChatVisible', 'isLargeDesktop']),
			...mapGetters('questions', ['filters']),
			hasPlan() {
				return !isEmpty(this.plan)
			},
			hasStats() {
				return !isEmpty(this.stats)
			},
		},
		methods: {
			...mapActions(['toggleChat']),
			...mapActions('questions', ['fetchDynamicFilters']),
			setPlanRoute() {
				this.planRoute = {
					name: 'questions-list',
					params: {
						presetFilters: [
							'quiz-planned.items[0]',
							'quiz-resolution.items[0]',
						],
					},
				}
			},
			getPlan() {
				return new Promise((resolve, reject) => {
					return axios.get(getApiUrl(`user_plan/${this.currentUserId}`))
						.then(({status, data}) => {
							let plan = data
							if (status === 204) {
								plan = {}
							}

							this.plan = plan
							return resolve(plan)
						})
						.catch((error) => reject(error))
				})
			},
			getStats() {
				return new Promise((resolve, reject) => {
					return axios.get(getApiUrl('quiz_questions/stats'))
						.then(({data}) => this.stats = data)
						.catch(e => this.stats = null)
				})
			},
			parseStats(source) {
				let stats = [{
					progress: source.resolved,
					progressNumber: `${source.resolved}/${source.total}`,
					score: Math.round(source.correct_perc),
					scoreNumber: `${source.correct}/${source.total}`,
					scoreTotal: Math.round(source.correct_perc_total),
					title: 'Cała baza',
					total: source.total,
				}]

				source.subjects.forEach((subject) => {
					stats.push({
						progress: subject.resolved,
						progressNumber: `${subject.resolved}/${subject.total}`,
						score: Math.round(subject.correct_perc),
						scoreNumber: `${subject.correct}/${subject.total}`,
						scoreTotal: Math.round(subject.correct_perc_total),
						title: subject.name,
						total: subject.total,
					})
				})

				return stats
			},
			scoreClass(score) {
				return score >= 56 ? 'is-success' : 'is-danger'
			},
		},
		mounted() {
			this.getPlan()
			this.getStats()
			isEmpty(this.filters)
				? this.fetchDynamicFilters().then(this.setPlanRoute)
				: this.setPlanRoute()
		},
		watch: {
			'$route' (to, from) {
				this.isChatVisible && this.toggleChat()
			}
		}
	}
</script>
