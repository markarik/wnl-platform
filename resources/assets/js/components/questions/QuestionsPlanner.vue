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
						<input class="slack-days-input" v-model="slackDays" type="number"/>
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

					<p class="has-text-centered margin top">
						<a class="button is-primary" @click="createPlan">Ułóż plan!</a>
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
				:fetchingQuestions="false"
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
		font-size: $font-size-minus-1
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

	.questions-plan-options
		+flex-center()
		margin-bottom: $margin-big

		.plan-option
			display: block
			line-height: $line-height-minus
</style>

<script>
	import axios from 'axios'
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
				unresolvedAndIncorrectCount: 0,
				customCount: 0,
				endDate: '',
				lastCustomFilters: [],
				selectedOption: 'unresolvedAndIncorrect',
				slackDays: 0,
				startDate: '',
			}
		},
		computed: {
			...mapGetters(['isChatMounted', 'isChatVisible', 'isLargeDesktop', 'isMobile']),
			...mapGetters('questions', [
				'activeFilters',
				'allQuestionsCount',
				'filters',
			]),
			counts() {
				return {
					all: this.allQuestionsCount,
					unresolvedAndIncorrect: this.unresolvedAndIncorrectCount,
					custom: this.customCount,
				}
			},
			endDateConfig() {
				return merge(defaultDateConfig(), {
					defaultDate: new Date('September 21, 2017 00:00:00'),
					minDate: 'today',
				})
			},
			planOptions() {
				return {
					unresolvedAndIncorrect: [
						'quiz-resolution.items[0]',
						'quiz-resolution.items[1]',
					],
					all: [],
					custom: this.lastCustomFilters,
				}
			},
			startDateConfig() {
				return merge(defaultDateConfig(), {
					defaultDate: 'today',
					minDate: 'today',
				})
			},
		},
		methods: {
			...mapActions('questions', [
				'activeFiltersSet',
				'activeFiltersToggle',
				'buildPlan',
				'fetchQuestionsCount',
				'fetchDynamicFilters',
			]),
			createPlan() {
				this.buildPlan({
					startDate: this.startDate,
					endDate: this.endDate,
					activeFilters: this.activeFilters,
					slackDays: this.slackDays
				})
			},
			onActiveFiltersChanged(payload) {
				this.activeFiltersToggle(payload)
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
	}
</script>
