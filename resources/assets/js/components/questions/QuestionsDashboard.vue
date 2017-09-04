<template>
	<div class="wnl-app-layout">
		<wnl-questions-navigation/>
		<div class="wnl-middle wnl-app-layout-main">
			<div v-if="!id" class="scrollable-main-container">
				<div class="questions-header">
					<div class="questions-breadcrumbs">
						<div class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-check-square-o"></i></span>
						</div>
						<div class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-angle-right"></i></span>
							<span>{{$t('questions.nav.dashboard')}}</span>
						</div>
					</div>
				</div>
				<div>
					<div class="questions-dashboard-heading">
						Plan pracy
					</div>
					<div v-if="plan === null" class="margin vertical">
						<wnl-text-loader/>
					</div>
					<wnl-questions-plan-progress v-else-if="hasPlan" :allowChange="false" :plan="plan"/>
					<div v-else>
						<p class="questions-plan-create">Zaplanuje pracę z pytaniami!<p>
						<p class="questions-plan-create-tip">
							Plan pracy pomoże Ci określić tempo, którym spokojnie rozwiążesz wszystkie pytania!
						</p>
						<p class="margin vertical has-text-centered">
							<router-link :to="{name: 'questions-planner'}">
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
						Twoje statystyki
					</div>
					<div class="questions-dashboard-subheading">
						<span class="icon is-small"><i class="fa fa-tasks"></i></span>
						Rozwiązane pytania
					</div>
					<div class="questions-stats">
						<div v-for="stats, index in statsResolved"
							class="stats-resolved"
							:class="{'is-first': index === 0}"
						>
							<span>{{stats.title}}</span>
							<progress class="progress is-success"
								:value="stats.progress"
								:max="stats.total"/>
							<span>{{stats.score}}</span>
						</div>
					</div>
					<div class="questions-dashboard-subheading">
						<span class="icon is-small"><i class="fa fa-tachometer"></i></span>
						Twoje wyniki
					</div>
					<div class="questions-stats">
						<div v-for="stats, index in statsScore"
							class="stats-score"
							:class="{'is-first': index === 0}"
						>
							<span>{{stats.title}}</span>
							<progress class="progress"
								:class="scoreClass(stats.score)"
								:value="stats.progress"
								:max="stats.total"/>
							<span>{{stats.score}}%</span>
						</div>
					</div>
				</div>
			</div>
			<router-view v-else :id="id"/>
		</div>
		<wnl-sidenav-slot
			:isDetached="!isChatMounted"
			:isVisible="isLargeDesktop"
			:hasChat="true"
		>
			<wnl-questions-feed/>
		</wnl-sidenav-slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

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
				console.log(score > 56)
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
