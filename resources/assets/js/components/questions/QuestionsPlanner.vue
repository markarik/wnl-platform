<template>
	<div class="wnl-app-layout" :class="{'is-mobile': isMobile}">
		<wnl-questions-navigation/>
		<div class="wnl-middle wnl-app-layout-main" :class="{'is-full-width': isLargeDesktop}">
			<div class="scrollable-main-container">
				<div class="questions-header">
					<div class="questions-breadcrumbs">
						<div class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-check-square-o"></i></span>
						</div>
						<div class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-angle-right"></i></span>
							<span>{{$t('questions.nav.planner')}}</span>
						</div>
					</div>
					<a v-if="isMobile" slot="heading" class="mobile-show-active-filters">
						<span>{{$t('questions.filters.show')}}</span>
						<span class="icon is-tiny">
							<i class="fa fa-sliders"></i>
						</span>
					</a>
				</div>
				<div class="questions-planner">
					<div class="questions-planner-heading">
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
							<wnl-datepicker v-model="startDate" :config="startDateConfig"/>
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
							<wnl-datepicker v-model="endDate" :config="endDateConfig"/>
							<p class="tip">
								{{$t('questions.plan.tips.endDate')}}
							</p>
						</div>
					</div>

					<div class="questions-planner-heading">
						{{$t('questions.plan.headings.slackDays')}}
					</div>
					<div class="slack-days">
						<input class="slack-days-input" min="0" :max="maxSlack" v-model="slackDays" type="number"/>
						<p class="tip">{{$t('questions.plan.tips.slackDays')}}</p>
					</div>

					<div class="questions-planner-heading">
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

					<div class="questions-planner-heading">
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
		<wnl-sidenav-slot
			:isDetached="!isChatMounted"
			:isVisible="isLargeDesktop"
			:hasChat="true"
		>
			<wnl-questions-filters
				v-show="selectedOption === 'custom'"
				:activeFilters="activeFilters"
				:fetchingQuestions="fetchingQuestions"
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
		margin-bottom: $margin-big

		.plan-option
			display: block
			line-height: $line-height-minus

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
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import {getApiUrl} from 'js/utils/env'

	const defaultDateConfig = () => {
		return {
			altInput: true,
			locale: pl,
		}
	}

	export default {
		name: 'QuestionsPlanner',
		components: {
			'wnl-datepicker': Datepicker,
			'wnl-questions-filters': QuestionsFilters,
			'wnl-questions-navigation': QuestionsNavigation,
			'wnl-sidenav-slot': SidenavSlot,
		},
		props: [],
		data() {
			return {
				customCount: 0,
				endDate: new Date('September 21, 2017 00:00:00'),
				fetchingQuestions: false,
				saving: false,
				selectedOption: 'unresolvedAndIncorrect',
				slackDays: 0,
				startDate: new Date(),
				unresolvedAndIncorrectCount: 0,
			}
		},
		computed: {
			...mapGetters(['isChatMounted', 'isChatVisible', 'isLargeDesktop', 'isMobile']),
			...mapGetters('questions', [
				'activeFilters',
				'allQuestionsCount',
				'filters',
			]),
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
				return merge(defaultDateConfig(), {
					minDate: 'today',
				})
			},
			maxSlack() {
				return this.datesRangeInDays
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
				return merge(defaultDateConfig(), {
					minDate: 'today',
				})
			},
		},
		methods: {
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
					slackDays: this.slackDays
				}).then(() => this.saving = false)
			},
			fetchMatchingQuestions(filters = []) {
				this.fetchingQuestions = true
				return this.fetchQuestions({
					doNotSaveFilters: true,
					filters,
					useSavedFilters: false,
				})
					.catch(error => $wnl.logger.error(error))
					.then(({data: {total}}) => {
						this.customCount = total
						this.fetchingQuestions = false
					})
			},
			onActiveFiltersChanged(payload) {
				this.activeFiltersToggle(payload).then(() => {
					this.fetchDynamicFilters()
					return this.fetchMatchingQuestions(this.activeFilters)
				})
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
		},
		mounted() {
			const presetFilters = this.planOptions[this.selectedOption]

			this.setFilters(presetFilters).then(() => {
				this.setUnresolvedAndIncorrectCount()
				this.fetchDynamicFilters()
				this.fetchQuestionsCount()
			})
		},
		watch: {
			selectedOption(to) {
				to === 'custom' && !this.counts.custom && this.fetchMatchingQuestions()
			}
		},
	}
</script>
