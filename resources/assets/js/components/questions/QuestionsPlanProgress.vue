<template>
	<div class="questions-plan-progress-container" :class="{'is-mobile': isMobile}">
		<div class="questions-plan-progress-heading">
			<span v-if="hasStarted" class="progress-day">
				{{$t('questions.plan.progress.day', {day: daysSoFar})}}
				/ {{plannedDaysCount}}
			</span>
			<span v-else></span>
			<a v-if="allowChange" class="button is-outlined is-small" @click="$emit('changePlan')">
				{{$t('questions.plan.change')}}
			</a>
		</div>
		<div class="box">
			<div class="questions-progress-heading">
				{{$t('questions.plan.progress.heading')}}
			</div>
			<div v-if="hasStarted">
				<p class="plan-progress-explain">
					{{$t('questions.plan.progress.explain')}}
				</p>
				<div class="plan-progress-bar">
					<progress class="progress is-success" :value="plan.stats.done" :max="plan.stats.total">
						{{donePercent}}%
					</progress>
					<div class="plan-progress-score">
						<span class="done">{{plan.stats.done}}</span>
						/ {{plan.stats.total}}
					</div>
				</div>
				<div class="plan-progress-average" :class="averageStatus">
					{{$t('questions.plan.progress.average.is')}}
					<span class="average">{{average}}</span>
					{{$t(`questions.plan.progress.average.${averageStatus}`)}}
					<span class="average-planned">{{averagePlanned}}</span>
					<div v-if="hasMoreQuestionsThanPlanned">
						<div v-if="questionsLeftPerDay">
							<span>{{$t('questions.plan.progress.average.newAverage.header')}}</span>
							<span class="new-average">{{questionsLeftPerDay}}</span>
							<span>{{$t('questions.plan.progress.average.newAverage.closure')}}</span>
						</div>
						<div v-if="planHasEnded">
							<span>{{$t('questions.plan.progress.planHasEnded')}}</span>
						</div>
					</div>
					<span v-if="averageStatus === 'greater'">
						{{$t('questions.plan.progress.average.congrats')}}
					</span>
				</div>
			</div>
			<div v-else>
				<p class="plan-starts">
					{{ $t('questions.plan.start.heading', {
						date: this.planStartDate.format('LL')
					}) }}
				</p>
				<p class="plan-progress-average">{{$t('questions.plan.start.tip', {
					average: averagePlanned,
					count: plan.stats.total,
					days: plannedDaysCount,
				})}}</p>
			</div>
		</div>
		<div v-if="hasStarted && average < averagePlanned" class="margin top has-text-centered">
			<router-link class="button is-primary" :to="plannedRoute">
				{{$t('questions.plan.solvePlanned')}}
			</router-link>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.questions-plan-progress-heading
		+flex-space-between()
		font-size: $font-size-minus-1
		margin-bottom: $margin-medium

		.progress-day
			font-weight: $font-weight-bold
			text-transform: uppercase

		.plan-change


	.questions-progress-heading
		letter-spacing: 1px
		margin: 0 0 $margin-small
		text-align: center
		text-transform: uppercase

	.plan-progress-explain
		color: $color-gray-dimmed
		font-size: $font-size-minus-1

	.plan-progress-bar
		+flex-space-between()

		.progress
			margin: 0 $margin-base 0 0

		.plan-progress-score
			font-size: $font-size-minus-1
			line-height: $line-height-none
			white-space: nowrap

			.done
				color: $color-green
				font-weight: $font-weight-bold

	.plan-starts
		font-size: $font-size-plus-1
		font-weight: bold
		text-align: center

	.plan-progress-average
		font-size: $font-size-minus-1
		margin-top: $margin-medium
		text-align: center

		.average,
		.average-planned,
		.new-average
			font-size: $font-size-base
			font-weight: $font-weight-bold

		&.less .average
			color: $color-orange

		&.greater .average
			color: $color-green

		.average-planned
			color: $color-green

		.new-average
			color: $color-purple
</style>

<script>
	import moment from 'moment'
	import {mapGetters, mapActions} from 'vuex'

	export default {
		name: 'QuestionsPlanProgress',
		data() {
			return {
				questionsLeftPerDay: 0,
				planHasEnded: false
			}
		},
		props: {
			allowChange: {
				default: true,
				type: Boolean,
			},
			plan: {
				required: true,
				type: Object,
			},
		},
		computed: {
			...mapGetters(['isMobile']),
			average() {
				return Math.ceil(this.plan.stats.done / (this.planResolvingSince - this.slackDaysUsed))
			},
			averagePlanned() {
				return Math.ceil(this.plan.stats.total / this.plannedDaysCount)
			},
			hasMoreQuestionsThanPlanned() {
				const questionsLeft = this.plan.stats.total - this.plan.stats.done
				const daysLeft = this.plannedDaysCount - this.daysSoFar + 1
				const questionsLeftPerDay =  Math.ceil(questionsLeft / daysLeft)

				if (questionsLeftPerDay > this.averagePlanned) {
					this.questionsLeftPerDay = questionsLeftPerDay
					return true
				} else if (this.daysSoFar > this.plannedDaysCount) {
					this.planHasEnded = true
					return true
				} else {
					return false
				}
			},
			averageStatus() {
				return this.average >= this.averagePlanned ? 'greater' : 'less'
			},
			created() {
				return moment(this.plan.created_at).format('LLL')
			},
			daysSoFar() {
				const diff = moment().startOf('day').diff(this.planStartDate, 'days')
				return diff < 0 ? 0 : diff + 1
			},
			donePercent() {
				return Math.round(this.plan.stats.done * 100 / this.plan.stats.total)
			},
			endDate() {
				return moment(this.plan.end_date.date)
			},
			hasStarted() {
				return this.daysSoFar > 0
			},
			plannedDaysCount() {
				return this.endDate.diff(this.planStartDate, 'days') - this.plan.slack_days_planned + 1
			},
			plannedRoute() {
				return {
					name: 'questions-list',
					params: {
						presetFilters: [
							'quiz-planned.items[0]',
						],
					},
				}
			},
			slackDaysUsed() {
				return this.plan.slack_days_planned - this.plan.slack_days_left
			},
			planStartDate() {
				return moment(this.plan.start_date.date)
			},
			planResolvingSince() {
				const resolvingStarted =  this.plan.calculated_start_date
					? moment(this.plan.calculated_start_date.date) : moment(this.plan.start_date.date)

				const diff = moment().startOf('day').diff(resolvingStarted, 'days')

				return diff < 0 ? 0 : diff + 1
			}
		},
	}
</script>
