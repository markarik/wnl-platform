<template>
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ $t('lessonsAvailability.sections.workDays') }}
				</div>
			</div>
		</div>
		<div class="days">
			<div class="day">
				<a v-for="day in days"
					class="panel-toggle"
					:class="{'is-active': isDayActive(day.dayNumber)}"
					:key="day.dayNumber"
					@click="toggleDay(day.dayNumber)"
				>{{ day.dayName }}
					<span class="icon is-small">
						<i class="fa"
							:class="[isDayActive(day.dayNumber) ? 'fa-check-circle' : 'fa-circle-o']"></i>
					</span>
				</a>
			</div>
		</div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ $t('lessonsAvailability.sections.availablePresets') }}
				</div>
			</div>
		</div>
		<div class="presets-control">
			<a v-for="name, preset in presets"
				class="panel-toggle"
				:class="{'is-active': isPresetActive(preset)}"
				:key="preset"
				@click="togglePreset(preset)"
				>{{name}}
				<span class="icon is-small">
					<i class="fa"
						:class="[isPresetActive(preset) ? 'fa-check-circle' : 'fa-circle-o']"></i>
				</span>
			</a>
		</div>
		<div class="presets-toggle" v-if="isPresetActive('daysPerLesson')">
			<div class="level wnl-screen-title">
				<div class="level-left">
					<div class="level-item">
						{{ $t('lessonsAvailability.secondSection.daysPerLesson') }}
					</div>
				</div>
			</div>
			<div class="presets">
				<div class="each-day-preset">
					<button
						@click="chooseWorkload(1)"
						class="button to-right"
						:class="{'is-active': this.workLoad === 1}"
						>{{ $t('lessonsAvailability.buttons.oneDayPerLesson') }}
					</button>
				</div>
				<div class="every-second-day-preset">
					<button
						@click="chooseWorkload(2)"
						class="button to-right"
						:class="{'is-active': this.workLoad === 2}"
						>{{ $t('lessonsAvailability.buttons.twoDaysPerLesson') }}
					</button>
				</div>
				<div class="every-three-days-preset">
					<button
						@click="chooseWorkload(3)"
						class="button to-right"
						:class="{'is-active': this.workLoad === 3}"
						>{{ $t('lessonsAvailability.buttons.threeDaysPerLesson') }}
					</button>
				</div>
				<div class="every-three-days-preset">
					<button
						@click="chooseWorkload(0)"
						class="button to-right"
						:class="{'is-active': this.workLoad === 0}"
						>{{ $t('lessonsAvailability.buttons.openAll')}}
					</button>
				</div>
			</div>
			<div class="level wnl-screen-title">
				<div class="level-left">
					<div class="level-item">
						{{ $t('lessonsAvailability.secondSection.startDate') }}
					</div>
				</div>
			</div>
			<div class="dates">
				<div class="start-date-picker">
					<label class="date-label" for="startDate">
						{{$t('questions.plan.headings.startDate')}}
						<span class="icon is-small">
							<i class="fa fa-hourglass-1"></i>
						</span>
					</label>
					<wnl-datepicker
						:withBorder="true"
						v-model="startDate"
						:config="startDateConfig"
						@onChange="onStartDateChange"/>
					<p class="tip">
						{{$t('questions.plan.tips.startDate')}}
					</p>
				</div>
			</div>
		</div>
		<div class="" v-if="isPresetActive('dateToDate')">
			<div class="level wnl-screen-title">
				<div class="level-left">
					<div class="level-item">
						{{ $t('lessonsAvailability.secondSection.dateToDate') }}
					</div>
				</div>
			</div>
			<div class="dates">
				<div class="start-date-picker">
					<label class="date-label" for="startDate">
						{{$t('questions.plan.headings.startDate')}}
						<span class="icon is-small">
							<i class="fa fa-hourglass-1"></i>
						</span>
					</label>
					<wnl-datepicker
						:withBorder="true"
						v-model="startDate"
						:config="startDateConfig"
						@onChange="onStartDateChange"/>
					<p class="tip">
						{{$t('questions.plan.tips.startDate')}}
					</p>
				</div>
				<div class="end-date-picker">
					<label class="date-label" for="endDate">
						{{$t('questions.plan.headings.endDate')}}
						<span class="icon is-small">
							<i class="fa fa-hourglass-1"></i>
						</span>
					</label>
					<wnl-datepicker
						:withBorder="true"
						v-model="endDate"
						:config="endDateConfig"
						@onChange="onEndDateChange"/>
					<p class="tip">
						{{$t('questions.plan.tips.endDate')}}
					</p>
				</div>
			</div>
		</div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ $t('lessonsAvailability.sections.acceptPlan') }}
				</div>
			</div>
		</div>
		<div class="accept-plan">
			<div class="accept-plan-button">
				<button
					@click="acceptPlan"
					class="button to-right is-info"
					>{{ $t('lessonsAvailability.buttons.acceptPlanButton') }}
				</button>
			</div>
		</div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ $t('user.lessonsAvailabilities.header')}}
				</div>
			</div>
		</div>
		<div class="groups">
			<ul class="groups-list" v-if="structure">
				<li class="group" v-for="(item, index) in groupsWithLessons"
					:key="index">
					<span class="item-toggle" @click="toggleItem(item)">
						<span class="icon is-small">
							<i class="toggle fa fa-angle-down"
								:class="{'fa-rotate-180': isOpen(item)}"></i>
						</span>
						<span class="item-name">{{item.name}}</span>
						<span class="subitems-count">
							({{item.lessons.length}})
						</span>
					</span>
					<ul class="subitems" v-if="isOpen(item)">
						<li class="subitem" v-for="(subitem, index) in item.lessons"
							:class="{'isEven': isEven(index)}"
							:key="index">
							<span class="subitem-name label"
								:class="{'is-grayed-out': !subitem.isAccessible}"
								>{{subitem.name}}</span>
							<div class="subitem-left-side">
								<div class="not-accesible" v-if="!subitem.isAccessible">
									{{ $t('lessonsAvailability.lessonNotAvilable') }}
								</div>
								<div class="datepicker" v-else>
									<wnl-datepicker
										:class="{'hasColorBackground': isEven(index)}"
										:value="getStartDate(subitem)"
										:subitemId="subitem.id"
										:config="startDateConfig"
										@onChange="(payload) => onStartDateChange(payload, subitem.id)"
									/>
								</div>
							</div>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.scrollable-main-container
		.days-info
			margin-bottom: $margin-small
		.days
			margin-bottom: $margin-big
			.day
				border: 10px

		.presets-control
			margin-bottom: $margin-big
			.presets
				display: flex
				justify-content: space-between
				flex-wrap: wrap
				margin-bottom: $margin-big

		.groups
			.groups-list
				.group
					margin-bottom: $margin-base
					.item-toggle
						cursor: pointer
						text-align: center
						width: 100%
						margin-bottom: $margin-small
						text-transform: uppercase
						color: $color-sky-blue
						.icon
							color: $color-gray
						.item-name
						.subitems-count
							color: $color-background-gray
							font-size: $font-size-minus-2

					.subitems
						display: flex
						flex-direction: column
						margin-bottom: $margin-small
						.subitem
							margin-bottom: $margin-small
							margin-top: $margin-small
							display: flex
							flex-direction: row-reverse
							justify-content: space-between
							min-height: 35px
							&.isEven
								background-color: $color-background-lightest-gray
							.subitem-name
								align-self: flex-end
								width: 65%
								color: $color-gray
								&.is-grayed-out
									color: $color-gray-dimmed
							.subitem-left-side
								margin-right: $margin-small
								display: flex
								align-items: center
								.not-accesible
									color: $color-gray-dimmed
									font-size: $font-size-plus-1
									text-align: center
									cursor: not-allowed
									min-width: 260px

</style>

<script>
import { pull } from 'lodash'
import { mapGetters, mapActions } from 'vuex'
import { resource } from 'js/utils/config'
import { getApiUrl } from 'js/utils/env'
import Datepicker from 'js/components/global/Datepicker'
import { pl } from 'flatpickr/dist/l10n/pl.js'
import { isEmpty, merge } from 'lodash'
import moment from 'moment'

export default {
	name: 'LessonsAvailabilities',
	components: {
		'wnl-datepicker': Datepicker,
	},
	data() {
		return {
			openGroups: [],
			endDateAlert: {
				text: 'Wybierz datę końcową',
				type: 'error',
				timeout: 2000,
			},
			activePresets: ['daysPerLesson', 'dateToDate'],
			startDate: new Date(),
			endDate: this.computedEndDate,
			workDays: [],
		}
	},
	computed: {
		...mapGetters(['currentUserId']),
		...mapGetters('course', [
			'name',
			'groups',
			'getRequiredLessons',
			'structure',
		]),
		...mapGetters('progress', [
			'getCompleteLessons',
		]),
		inProgressLessonsLength() {
			return Object.keys(this.getRequiredLessons).filter(requiredQuestion => {
				return !this.completedLessons.includes(Number(requiredQuestion))
			}).length
		},
		computedEndDate() {
			if (this.workLoad === 0) {
				return this.endDate = this.startDate
			} else {
				return this.endDate
			}
		},
		minimumEndDate() {
				if (this.workDays.length > 4) {
					return moment(this.startDate).add(this.inProgressLessonsLength, 'days').toDate()
				}
		},
		completedLessons() {
			return this.getCompleteLessons(1).map(lesson => lesson.id)
		},
		startDateConfig() {
			return merge(this.defaultDateConfig(), {
				minDate: 'today',
			})
		},
		endDateConfig() {
			return merge(this.defaultDateConfig(), {
				minDate: this.minimumEndDate,
			})
		},
		groupsWithLessons() {
			return this.groups.map(groupId => {
				const group = this.structure[resource('groups')][groupId]
				return {
					...group,
					lessons: group[resource('lessons')].map(lessonId => {
						return this.structure[resource('lessons')][lessonId]
					})
				}
			})
		},
		presets() {
			return {
				daysPerLesson: 'Ile dnia na lekcję?',
				dateToDate: 'Od daty do daty',
			}
		},
		computedWorkDays() {
			this.workDays.sort((a, b) => {
				return a - b
			})
		},
		days() {
			let days = [
				{
					dayName: 'Ponedziałek',
					dayNumber: 1
				},
				{
					dayName: 'Wtorek',
					dayNumber: 2
				},
				{
					dayName: 'Środa',
					dayNumber: 3
				},
				{
					dayName: 'Czwartek',
					dayNumber: 4
				},
				{
					dayName: 'Piątek',
					dayNumber: 5
				},
				{
					dayName: 'Sobota',
					dayNumber: 6
				},
				{
					dayName: 'Niedziela',
					dayNumber: 7
				},
			]
			return days
		},
		daysQuantity() {
			let startDate = moment(this.startDate);
			let endDate = moment(this.endDate);
			return endDate.diff(startDate, 'days')
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('course', ['setLessonAvailabilityStatus']),
		...mapActions(['toggleOverlay']),
		isPresetActive(preset) {
			return this.activePresets[0] === preset
		},
		chooseWorkload(workLoad) {
			this.workLoad = workLoad
		},
		defaultDateConfig() {
			return {
				altInput: true,
				disableMobile: true,
				locale: pl,
			}
		},
		onEndDateChange(payload) {
			if (isEmpty(payload)) this.endDate = null
		},
		onStartDateChange(payload) {
			if (isEmpty(payload)) this.startDate = null
		},
		togglePreset(preset) {
			return this.activePresets = [preset]
		},
		isDayActive(dayNumber) {
			let index = this.workDays.indexOf(dayNumber)
			if (index === -1) {
				return false
			} else {
				return true
			}
		},
		toggleDay(dayNumber) {
			let index = this.workDays.indexOf(dayNumber)
			if (index === -1) {
				return this.workDays.push(dayNumber)
			} else {
				return this.workDays.splice(index, 1)
			}
		},
		getStartDate(item) {
			return new Date (item.startDate*1000)
		},
		acceptPlan() {
			axios.put(getApiUrl(`user_lesson/${this.currentUserId}`), {
				user_id: this.currentUserId,
				work_days: this.workDays,
				work_load: this.workLoad,
				start_date: this.startDate,
				end_date: this.endDate,
				days_quantity: this.daysQuantity,
				preset_active: this.activePresets,
			})
		},
		isOpen(item) {
			return this.openGroups.indexOf(item.id) > -1
		},
		isEven(index) {
			return index % 2 === 0
		},
		toggleItem(item) {
			if (this.openGroups.indexOf(item.id) === -1) {
				this.openGroups.push(item.id)
			} else {
				const index = this.openGroups.indexOf(item.id)
				if (index > -1) {
					this.openGroups.splice(index, 1)
				}
			}
		},
		onStartDateChange(payload, lessonId) {
			const date = payload[0]
			const diff = moment().startOf('day').diff(date, 'days')

			this.setLessonAvailabilityStatus({lessonId, status: diff >= 0})

			axios.put(getApiUrl(`user_lesson/${this.currentUserId}/${lessonId}`), {
				date: payload[0]
			}).then(() => {
				this.addAutoDismissableAlert(this.alertSuccess)
			}).catch((error) => {
				$wnl.logger.error(error)
				this.addAutoDismissableAlert(this.alertError)
			})
		}
	}
}
</script>
