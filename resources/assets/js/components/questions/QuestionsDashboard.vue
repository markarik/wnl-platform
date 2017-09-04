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
						<div class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-angle-right"></i></span>
							<span>{{$t('questions.nav.dashboard')}}</span>
						</div>
						<div v-if="id" class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-angle-right"></i></span>
							<span>#{{id}}</span>
						</div>
					</div>
				</div>
				<div v-if="!id">
					<div class="questions-dashboard-plan">
						<div class="questions-dashboard-heading">
							<span class="icon is-small"><i class="fa fa-calendar"></i></span>
							Plan pracy
						</div>
						<div v-if="plan === null" class="margin vertical">
							<wnl-text-loader/>
						</div>
						<wnl-questions-plan-progress v-else-if="hasPlan" :allowChange="false" :plan="plan"/>
						<div class="questions-plan-create" v-else>
							<p class="questions-plan-create-heading">Zaplanuj pracę z pytaniami!<p>
							<p class="questions-plan-create-tip">
								Plan pracy pomoże Ci określić tempo, którym spokojnie rozwiążesz wszystkie pytania!
							</p>
							<p class="margin vertical has-text-centered">
								<router-link class="button is-primary is-outlined" :to="{name: 'questions-planner'}">
									Zaplanuj pracę
								</router-link>
							</p>
						</div>
					</div>
					<div v-if="stats === null">
						Ups... Niestety, nie udało nam się załadować Twoich statystyk... Spróbujesz jeszcze raz? Jeśli problem będzie się powtarzał, daj nam znać w zakładce Pomoc > Pomoc techniczna. Przepraszamy!
					</div>
					<div v-else-if="!hasStats" class="margin vertical">
						<wnl-text-loader/>
					</div>
					<div v-else>
						<div class="questions-dashboard-heading">
							<span class="icon is-small"><i class="fa fa-bar-chart"></i></span>
							Twoje statystyki
						</div>
						<div class="questions-dashboard-subheading">
							<span class="icon is-small"><i class="fa fa-tasks"></i></span>
							Rozwiązane pytania
						</div>
						<div class="questions-stats">
							<div v-for="stats, index in statsResolved"
								class="stats-item stats-resolved"
								:class="{'is-first': index === 0}"
							>
								<span class="stats-title">{{stats.title}}</span>
								<div class="bar-and-score">
									<progress class="progress is-success"
										:value="stats.progress"
										:max="stats.total"/>
									<span class="stats-score">{{stats.score}}</span>
								</div>
							</div>
						</div>
						<div class="questions-dashboard-subheading">
							<span class="icon is-small"><i class="fa fa-tachometer"></i></span>
							Twoje wyniki
						</div>
						<div class="questions-stats">
							<div v-for="stats, index in statsScore"
								class="stats-item stats-score"
								:class="{'is-first': index === 0}"
							>
								<span class="stats-title">{{stats.title}}</span>
								<div class="bar-and-score">
									<progress class="progress"
										:class="scoreClass(stats.score)"
										:value="stats.progress"
										:max="stats.total"/>
									<span class="stats-score">{{stats.score}}%</span>
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
				<p class="questions-feed-heading">
					<span class="icon is-small"><i class="fa fa-commenting-o"></i></span>
					Ostatnie dyskusje
				</p>
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

			&.is-first
				font-size: $font-size-base
				font-weight: $font-weight-bold

				.progress
					height: 1rem

			.stats-title
				margin-right: $margin-medium
				width: 150px

			.bar-and-score
				+flex-space-between()
				flex: 1 auto

			.progress
				flex: 1 auto
				height: 4px
				margin-bottom: 0
				margin-right: $margin-medium
				min-width: 150px

			.stats-score
				width: 100px

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
			...mapGetters(['currentUserId','isChatMounted', 'isLargeDesktop']),
			...mapGetters('questions', ['filters']),
			hasPlan() {
				return !isEmpty(this.plan)
			},
			hasStats() {
				return !isEmpty(this.stats)
			},
			statsScore() {
				let stats = [{
					title: 'Cała baza',
					progress: this.stats.correct,
					total: this.stats.resolved,
					score: Math.round(this.stats.correct_perc),
				}]

				this.stats.subjects.forEach((subject) => {
					stats.push({
						title: subject.name,
						progress: subject.correct,
						total: subject.resolved,
						score: Math.round(subject.correct_perc),
					})
				})

				return stats
			},
			statsResolved() {
				let stats = [{
					title: 'Cała baza',
					progress: this.stats.resolved,
					total: this.stats.total,
					score: `${this.stats.resolved}/${this.stats.total}`,
				}]

				this.stats.subjects.forEach((subject) => {
					stats.push({
						title: subject.name,
						progress: subject.resolved,
						total: subject.total,
						score: `${subject.resolved}/${subject.total}`,
					})
				})

				return stats
			},
		},
		methods: {
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
			scoreClass(score) {
				return score > 56 ? 'is-success' : 'is-danger'
			},
		},
		mounted() {
			this.getPlan()
			this.getStats()
			isEmpty(this.filters)
				? this.fetchDynamicFilters().then(this.setPlanRoute)
				: this.setPlanRoute()
		},
	}
</script>
