<template>
	<div class="wnl-app-layout" :class="{'is-mobile': isMobile}">
		<wnl-questions-navigation/>
		<div class="wnl-middle wnl-app-layout-main" :class="{'is-full-width': isLargeDesktop}">
			<div class="scrollable-main-container">
				<div class="questions-header questions-plan-header">
					<div class="questions-breadcrumbs">
						<div class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-check-square-o"></i></span>
						</div>
						<div class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-angle-right"></i></span>
							<span>{{$t('questions.nav.planner')}}</span>
						</div>
					</div>
				</div>
				<div v-if="plan === null" class="margin vertical">
					<wnl-text-loader/>
				</div>
				<div v-else-if="hasPlan && !showPlanner" class="questions-plan-progress">
					<wnl-questions-plan-progress :plan="plan" @changePlan="showPlanner = true"/>
				</div>
				<div v-else class="questions-planner">
					<div class="questions-planner-heading">
						<span>
							{{$t(`questions.plan.headings.${plannerHeading}`)}}
						</span>
						<a v-if="hasPlan"
							class="button is-small is-primary is-outlined"
							@click="showPlanner = false"
						>
							{{$t('questions.plan.dontChange')}}
						</a>
					</div>
					<div class="planner-form-heading">
						{{$t('questions.plan.headings.dates')}}
					</div>
					<div class="dates columns">
						<div class="column">
							<label class="date-label" for="startDate">
								{{$t('questions.plan.headings.startDate')}}
								<span class="icon is-small">
									<i class="fa fa-hourglass-1"></i>
								</span>
							</label>
							<wnl-datepicker :withBorder="true" v-model="startDate" :config="startDateConfig" @onChange="onStartDateChange"/>
							<p class="tip">
								{{$t('questions.plan.tips.startDate')}}
							</p>
						</div>
						<div class="column">
							<label class="date-label" for="endDate">
								{{$t('questions.plan.headings.endDate')}}
								<span class="icon is-small">
									<i class="fa fa-hourglass-3"></i>
								</span>
							</label>
							<wnl-datepicker :withBorder="true" v-model="endDate" :config="endDateConfig" @onChange="onEndDateChange"/>
							<p class="tip">
								{{$t('questions.plan.tips.endDate')}}
							</p>
						</div>
					</div>

					<div v-if="!hasMissingDates">
						<div class="planner-form-heading">
							{{$t('questions.plan.headings.slackDays')}}
						</div>
						<div class="slack-days">
							<input class="slack-days-input" min="0" :max="maxSlack" v-model="slackDays" type="number"/>
							<p class="tip">{{$t('questions.plan.tips.slackDays')}}</p>
						</div>

						<div class="planner-form-heading">
							{{$t('questions.plan.headings.questions')}}
						</div>
						<div class="questions-plan-options">
							<div v-for="filters, option in planOptions" :key="option">
								<a
									class="plan-option panel-toggle"
									:class="{'is-active': selectedOption === option}"
									@click="selectOption(option, filters)"
								>
									{{$t(`questions.plan.options.${option}`)}}
								</a>
								<p class="tip">
									<span v-if="counts[option] > 0">({{counts[option]}})</span>
									<span v-else>&nbsp;</span>
								</p>
							</div>
						</div>
						<div class="preserveProgress control" v-if="hasPlan">
							<input id="preserveProgress" type="checkbox" class="checkbox" v-model="preserveProgress">
							<label for="preserveProgress">{{$t('questions.filters.preserveProgress')}}</label>
						</div>
						<p class="tip has-text-centered">{{$t('questions.filters.preserveProgressTip')}}</p>
						<div  v-if="!isLargeDesktop && selectedOption === 'custom'" class="questions-plan-toggle-filters">
							<div class="active-filters tip">
								<span>{{$t('questions.filters.activeHeading')}}:</span>
								<span v-if="activeFiltersNames.length > 0">
									{{activeFiltersNames}}
								</span>
								<span v-else>
									{{$t('questions.filters.allQuestions')}}
								</span>
							</div>
							<a class="button is-small is-outlined is-primary" @click="toggleChat">
								<span>{{$t('questions.filters.show')}}</span>
								<span class="icon is-tiny">
									<i class="fa fa-sliders"></i>
								</span>
							</a>
						</div>

						<div class="planner-form-heading">
							{{$t('questions.plan.headings.summary')}}
						</div>
						<div class="questions-plan-summary">
							<span class="count">{{currentPlan.count}}</span>
							{{$t('questions.plan.summaryCount')}}
							<span class="days">{{currentPlan.days}}</span>
							{{$t('questions.plan.summaryDays')}}
							<span class="average" :class="averageClass">{{currentPlan.average}}</span>
							{{$t('questions.plan.summaryAverage')}}
						</div>
						<div class="questions-plan-tip">
							<p class="tip">
								{{$t('questions.plan.summaryTip')}}
							</p>
						</div>

						<p class="has-text-centered margin top">
							<a class="button is-primary" :class="{'is-loading': saving}" @click="createPlan">
								{{$t('questions.plan.submit')}}
							</a>
						</p>
					</div>
				</div>
			</div>
		</div>
		<wnl-sidenav-slot
			:isDetached="!isChatMounted"
			:isVisible="isLargeDesktop || isChatVisible"
			:hasChat="true"
		>
			<wnl-questions-filters
				v-show="showPlanner && selectedOption === 'custom'"
				:activeFilters="activeFilters"
				:fetchingData="fetchingQuestions"
				:filters="filters"
				@activeFiltersChanged="onActiveFiltersChanged"
			/>
		</wnl-sidenav-slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.wnl-app-layout-main.is-full-width
		max-width: 100%

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

	.tip
		color: $color-background-gray
		font-size: $font-size-minus-2
		text-align: center

	.questions-planner-heading
		+flex-space-between()
		margin-top: $margin-base

		span
			font-size: $font-size-minus-1
			font-weight: $font-weight-bold
			text-transform: uppercase

	.planner-form-heading
		border-bottom: $border-light-gray
		letter-spacing: 1px
		margin: $margin-base 0 $margin-small
		text-align: center
		text-transform: uppercase

		.icon
			color: $color-background-gray
			margin-right: $margin-small

	.dates
		margin-bottom: $margin-big

		.icon
			color: $color-background-gray
			margin-left: $margin-small

		.date-label
			display: block
			font-size: $font-size-minus-1
			text-align: center
			text-transform: uppercase
			width: 100%

	.preserveProgress
		align-items: center
		color: $color-gray-dimmed
		display: flex
		justify-content: center
		font-size: $font-size-minus-2
		text-transform: uppercase

	.slack-days
		+flex-center()
		flex-direction: column
		margin-bottom: $margin-big

		.slack-days-input
			border: 0
			border-bottom: 1px solid $color-ocean-blue
			border-radius: 0
			box-shadow: none
			display: block
			font-size: $font-size-plus-1
			font-weight: bold
			margin-top: $margin-small
			outline: 0
			text-align: center
			width: 250px

	.questions-plan-options
		+flex-center()
		margin-bottom: $margin-medium

		.plan-option
			display: block
			line-height: $line-height-minus

	.questions-plan-toggle-filters
		margin: -$margin-base 0 $margin-big
		text-align: center

	.questions-plan-summary
		margin: $margin-medium 0
		text-align: center

		.average, .count, .days
			font-size: $font-size-plus-2
			font-weight: $font-weight-bold

		.count
			color: $color-purple

		.days
			color: $color-blue

		.average
			&.easy
				color: $color-green

			&.medium
				color: $color-yellow

			&.hard
				color: $color-red

	.questions-plan-progress
		margin: $margin-base 0 $margin-big
</style>

<script>
	import axios from 'axios'
	import moment from 'moment'
	import {pl} from 'flatpickr/dist/l10n/pl.js'
	import {isEmpty, merge} from 'lodash'
	import {mapActions, mapGetters} from 'vuex'

	import Datepicker from 'js/components/global/Datepicker'
	import QuestionsFilters from 'js/components/questions/QuestionsFilters'
	import QuestionsNavigation from 'js/components/questions/QuestionsNavigation'
	import QuestionsPlanProgress from 'js/components/questions/QuestionsPlanProgress'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import {getApiUrl} from 'js/utils/env'
	import features from 'js/consts/events_map/features.json';
	import context from 'js/consts/events_map/context.json';

	export default {
		name: 'QuestionsPlanner',
		components: {
			'wnl-datepicker': Datepicker,
			'wnl-questions-filters': QuestionsFilters,
			'wnl-questions-navigation': QuestionsNavigation,
			'wnl-questions-plan-progress': QuestionsPlanProgress,
			'wnl-sidenav-slot': SidenavSlot,
		},
		props: [],
		data() {
			return {
				customCount: 0,
				endDate: null,
				fetchingQuestions: false,
				plan: null,
				saving: false,
				selectedOption: 'unresolvedAndIncorrect',
				showPlanner: false,
				slackDays: 0,
				startDate: new Date(),
				unresolvedAndIncorrectCount: 0,
				preserveProgress: true
			}
		},
		computed: {
			...mapGetters(['currentUserId', 'isChatMounted', 'isChatVisible', 'isLargeDesktop', 'isMobile']),
			...mapGetters('questions', [
				'activeFilters',
				'activeFiltersObjects',
				'allQuestionsCount',
				'filters',
			]),
			activeFiltersNames() {
				return this.activeFiltersObjects.map(filter => {
					return filter.hasOwnProperty('name')
						? filter.name
						: filter.hasOwnProperty('message')
							? this.$t(`questions.filters.items.${filter.message}`)
							: this.$t(`questions.filters.items.${filter.value}`)
				}).join(', ')
			},
			averageClass() {
				return this.currentPlan.average <= 100
					? 'easy'
					: this.currentPlan.average <= 200
						? 'medium'
						: 'hard'
			},
			counts() {
				return {
					all: this.allQuestionsCount,
					unresolvedAndIncorrect: this.unresolvedAndIncorrectCount,
					custom: this.customCount,
				}
			},
			currentPlan() {
				const count = this.counts[this.selectedOption]
				const days = this.datesRangeInDays + 1 - this.slackDays

				return {average: Math.ceil(count/days), count, days}
			},
			datesRangeInDays() {
				return moment(this.endDate).diff(moment(this.startDate), 'days')
			},
			endDateConfig() {
				return merge(this.defaultDateConfig(), {
					minDate: this.startDate,
				})
			},
			hasMissingDates() {
				return this.startDate === null || this.endDate === null
			},
			hasPlan() {
				return !isEmpty(this.plan)
			},
			maxSlack() {
				return this.datesRangeInDays
			},
			plannerHeading() {
				return this.hasPlan ? 'change' : 'create'
			},
			planOptions() {
				return {
					unresolvedAndIncorrect: [
						'quiz-resolution.items[0]',
						'quiz-resolution.items[1]',
					],
					all: [],
					custom: [],
				}
			},
			startDateConfig() {
				return merge(this.defaultDateConfig(), {
					minDate: 'today',
				})
			},
		},
		methods: {
			...mapActions(['toggleChat']),
			...mapActions('questions', [
				'activeFiltersSet',
				'activeFiltersToggle',
				'buildPlan',
				'fetchDynamicFilters',
				'fetchQuestions',
				'fetchQuestionsCount',
			]),
			createPlan() {
				this.saving = true
				this.buildPlan({
					startDate: this.startDate,
					endDate: this.endDate,
					activeFilters: this.activeFilters,
					slackDays: this.slackDays,
					preserveProgress: this.preserveProgress
				})
				.then(({status, data}) => this.plan = data)
				.then(this.fetchDynamicFilters)
				.then(() => {
					this.saving = false
					this.showPlanner = false
					this.$trackUserEvent({
						context: context.questions_bank.value,
						feature: features.quiz_planner.value,
						action: features.quiz_planner.actions.save.value
					})
				})
			},
			defaultDateConfig() {
				return {
					altInput: true,
					disableMobile: true,
					locale: pl,
				}
			},
			fetchMatchingQuestions(filters = []) {
				this.fetchingQuestions = true
				return this.fetchQuestions({
					saveFilters: false,
					useSavedFilters: false,
					filters,
				})
					.catch(error => $wnl.logger.error(error))
					.then(({data: {total}}) => {
						this.customCount = total
						this.fetchingQuestions = false
					})
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
			onActiveFiltersChanged(payload) {
				if (payload.refresh) {
					return this.activeFiltersToggle(payload).then(() => {
						this.fetchDynamicFilters()
						return this.fetchMatchingQuestions(this.activeFilters)
					})
				}

				this.activeFiltersToggle(payload)
			},
			onEndDateChange(payload) {
				if (isEmpty(payload)) this.endDate = null
			},
			onStartDateChange(payload) {
				if (isEmpty(payload)) this.startDate = null
			},
			selectOption(option, filters) {
				this.selectedOption = option
				this.activeFiltersSet(filters)
				return this.fetchDynamicFilters()
			},
			setFilters(filters) {
				return new Promise((resolve, reject) => {
					if (!isEmpty(this.filters)) {
						this.activeFiltersSet(filters)
						return resolve()
					}

					this.fetchDynamicFilters().then(() => {
						this.activeFiltersSet(filters)
						return resolve()
					})
				})
			},
			setUnresolvedAndIncorrectCount() {
				axios.get(getApiUrl('quiz_questions/stats')).then(({data: {correct, total}}) => {
					this.unresolvedAndIncorrectCount = total - correct
				})
			},
			setupPlanner() {
				const presetFilters = this.planOptions[this.selectedOption]

				this.showPlanner = true
				this.setFilters(presetFilters).then(() => {
					this.fetchDynamicFilters()
					this.setUnresolvedAndIncorrectCount()
					this.fetchQuestionsCount()
				})
			}
		},
		mounted() {
			this.$trackUserEvent({
				feature: features.quiz_planner.value,
				context: context.questions_bank.value,
				actions: features.quiz_planner.actions.open.value
			})
			this.getPlan().then(plan => {
				isEmpty(plan) ? this.setupPlanner() : this.fetchDynamicFilters()
			})
		},
		watch: {
			selectedOption(to) {
				to === 'custom' && !this.counts.custom && this.fetchMatchingQuestions()
			},
			showPlanner(to) {
				to && isEmpty(this.counts.all) && this.setupPlanner()
			}
		},
	}
</script>
