<template>
	<div class="scrollable-main-container">
		<div class="wnl-overlay" v-if="isLoading">
			<span class="loader"></span>
			<span class="loader-text">{{ $t('lessonsAvailability.loader') }}</span>
		</div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ $t('lessonsAvailability.header') }}
				</div>
			</div>
		</div>
		<div class="views-control">
			<div class="view">
				<a v-for="name, view in views"
					class="panel-toggle preset"
					:class="{'is-active': isViewActive(view)}"
					:key="view"
					@click="toggleView(view)"
					>{{name}}
					<span class="icon is-small">
						<i class="fa"
						:class="[isViewActive(view) ? 'fa-check-circle' : 'fa-circle-o']"></i>
					</span>
				</a>
			</div>
		</div>
		<div class="presets-view" v-if="activeView === 'presetsView'">
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
				<div class="preset">
					<a v-for="name, preset in presets"
						class="panel-toggle preset"
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
			</div>
			<div class="work-load-toggle" v-if="isPresetActive('daysPerLesson')">
				<div class="level wnl-screen-title">
					<div class="level-left">
						<div class="level-item">
							{{ $t('lessonsAvailability.secondSection.daysPerLesson') }}
						</div>
					</div>
				</div>
				<div class="work-load-buttons">
					<div class="work-load-button panel-toggle" v-for="workLoad in availableWorkLoads">
						<button
							@click="chooseWorkload(workLoad.workLoad)"
							class="button to-right"
							:class="{'is-active': this.workLoad === workLoad.workLoad}"
							>{{ $t(workLoad.translation) }}
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
					<div class="date">
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
								@onChange="onPresetStartDateChange"/>
							<p class="tip">
								{{$t('questions.plan.tips.startDate')}}
							</p>
						</div>
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
					<div class="date">
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
								@onChange="onPresetStartDateChange"/>
							<p class="tip">
								{{$t('questions.plan.tips.startDate')}}
							</p>
						</div>
					</div>
					<div class="date">
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
		</div>
		<div class="all-lessons-view"  v-if="activeView === 'lessonsView'">
			<div class="level wnl-screen-title">
				<div class="level-left">
					<div class="level-item big strong">
						{{ $t('lessonsAvailability.allLessons')}}
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
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.scrollable-main-container
		.wnl-overlay
			align-items: center
			background: rgba(255, 255, 255, 0.9)
			bottom: 0
			display: flex
			flex-direction: column
			justify-content: center
			left: 0
			position: fixed
			right: 0
			top: 0
			z-index: $z-index-overlay

			.loader
				height: 40px
				width: 40px

			.loader-text
				margin-top: $margin-small
				color: $color-ocean-blue

		.views-control
			display: flex
			flex-wrap: wrap
			justify-content: space-around
			margin-bottom: $margin-base
			.view
				display: flex
				justify-content: space-around
				flex-wrap: wrap
				border: 10px

		.days-info
			margin-bottom: $margin-small

		.days
			display: flex
			flex-wrap: wrap
			justify-content: space-around
			margin-bottom: $margin-big
			.day
				display: flex
				justify-content: space-around
				flex-wrap: wrap

		.presets-control
			margin-bottom: $margin-big
			display: flex
			flex-wrap: wrap
			justify-content: space-around
			.preset
				flex-wrap: wrap
				display: flex
				justify-content: space-around
				.icon
					vertical-align: baseline

		.work-load-buttons
			display: flex
			flex-wrap: wrap
			justify-content: space-around
			margin-bottom: $margin-base
			.work-load-button
				display: flex
				justify-content: space-around
				flex-wrap: wrap
				border: 10px

		.dates
			.date
				margin-bottom: $margin-big
				label, .tip
					width: 100%
					display: inline-block
					text-align: center

		.accept-plan
			margin-bottom: $margin-small
			display: flex
			justify-content: space-around

		.lessons-view
			margin-bottom: $margin-base

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
import { mapGetters, mapActions } from 'vuex'
import { resource } from 'js/utils/config'
import { getApiUrl } from 'js/utils/env'
import Datepicker from 'js/components/global/Datepicker'
import { pl } from 'flatpickr/dist/l10n/pl.js'
import { isEmpty, merge, pull } from 'lodash'
import moment from 'moment'

export default {
	name: 'LessonsAvailabilities',
	components: {
		'wnl-datepicker': Datepicker,
	},
	data() {
		return {
			isLoading: false,
			openGroups: [],
			activePresets: ['daysPerLesson', 'dateToDate'],
			startDate: new Date(),
			endDate: null,
			workDays: [1, 2, 3, 4, 5],
			workLoad: null,
			activeView: 'presetsView',
			activeViewNames: {
				closed: 'Pokaż lekcje',
				open: 'Schowaj lekcje'
			},
			alertSuccess: {
				text: 'Udało się zmienić datę! :)',
				type: 'success',
			},
			alertError: {
				text: 'Nie udało się zmienić daty, spróbuj jeszcze raz!',
				type: 'error',
			},
			defaultDateConfig: {
				altInput: true,
				disableMobile: true,
				locale: pl,
			},
		}
	},
	computed: {
		...mapGetters(['currentUserId', 'currentUserSubscriptionDates']),
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
			return Object.keys(this.getRequiredLessons).filter(requiredLesson => {
				return !this.completedLessons.includes(Number(requiredLesson))
			}).length
		},
		minimumEndDate() {
			return moment(this.startDate).add(Math.ceil(this.inProgressLessonsLength * 7 / this.workDays.length), 'days').toDate()
		},
		completedLessons() {
			return this.getCompleteLessons(1).map(lesson => lesson.id)
		},
		startDateConfig() {
			return {
				...this.defaultDateConfig,
				minDate: 'today',
			}
		},
		endDateConfig() {
			return {
				...this.defaultDateConfig,
				minDate: this.minimumEndDate,
			}
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
				daysPerLesson: 'Ile dni na lekcję?',
				dateToDate: 'Od daty do daty',
			}
		},
		views() {
			return {
				presetsView: 'Automatyczne plany lekcji',
				lessonsView: 'Widok zaawansowany',
			}
		},
		computedWorkDays() {
			this.workDays.sort((a, b) => {
				return a - b
			})
		},
		availableWorkLoads() {
			let availableWorkLoads = [
				{
					workLoad: 1,
					translation: 'lessonsAvailability.buttons.oneDayPerLesson',
				},
				{
					workLoad: 2,
					translation: 'lessonsAvailability.buttons.twoDaysPerLesson',
				},
				{
					workLoad: 3,
					translation: 'lessonsAvailability.buttons.threeDaysPerLesson',
				},
				{
					workLoad: 0,
					translation: 'lessonsAvailability.buttons.openAll',
				}
			]
			return availableWorkLoads
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
			if (this.activePresets === 'dateToDate') {
				const startDate = moment(this.startDate);
				const endDate = moment(this.endDate);
				return endDate.diff(startDate, 'days')
			}
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('course', ['setLessonAvailabilityStatus', 'updateLessonStartDate']),
		...mapActions(['toggleOverlay']),
		isPresetActive(preset) {
			return this.activePresets === preset
		},
		isViewActive(view) {
			return this.activeView === view
		},
		chooseWorkload(workLoad) {
			this.workLoad = workLoad
		},
		onEndDateChange(payload) {
			if (isEmpty(payload)) this.endDate = null
		},
		onStartDateChange(payload) {
			if (isEmpty(payload)) this.startDate = null
		},
		toggleView(view) {
			return this.activeView = view
		},
		togglePreset(preset) {
			return this.activePresets = preset
		},
		toggleLessonsView() {
			return this.activeView = !this.activeView
		},
		isDayActive(dayNumber) {
			return this.workDays.includes(dayNumber)
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
			if (this.activePresets === 'dateToDate') {
				this.workLoad = null
			}
			if (isEmpty(this.workDays)) {
				return this.addAutoDismissableAlert({
					text: `Wybierz przynajmniej jeden dzień, w którym chcesz aby otwierały się lekcje :)`,
					type: 'error',
					timeout: 3000,
				})
			} else if (this.workLoad === null && this.activePresets === 'daysPerLesson') {
				return this.addAutoDismissableAlert({
					text: `Zaznacz, ile dni chcesz poświęcić na jedną lekcję :)`,
					type: 'error',
					timeout: 3000,
				})
			} else if (this.endDate === null && this.activePresets === 'dateToDate') {
				return this.addAutoDismissableAlert({
					text: `Wybierz datę, w której ma zakończyć się nauka :)`,
					type: 'error',
					timeout: 3000,
				})
			} else {
				this.isLoading = true
				axios.put(getApiUrl(`user_lesson/${this.currentUserId}`), {
					work_days: this.workDays,
					work_load: this.workLoad,
					start_date: this.startDate,
					end_date: this.endDate,
					days_quantity: this.daysQuantity,
					preset_active: this.activePresets,
				}).then((response) => {
					this.isLoading = false
					if (response.status === 200) {
						console.log(response.data);
						response.data.lessons.forEach((lesson) => {
							this.updateLessonStartDate({
								lessonId: lesson.id,
								start_date: lesson.startDate
							})
							if (moment(this.currentUserSubscriptionDates).isSameOrAfter(moment(response.data.end_date))) {
								this.addAutoDismissableAlert({
									text: `Data otwarcia ostatniej lekcji: ${moment(response.data.end_date * 1000).locale('pl').format('LL')}, wypada poza datą Twojej subskrypcji: ${moment(this.currentUserSubscriptionDates).locale('pl').format('LL')}. Plan został ustalony według Twoich ustawień.`,
									type: 'error',
									timeout: 10000,
								})
							} else {
								this.addAutoDismissableAlert({
									text: `Udało się zmienić daty. Data otwarcia ostatniej lekcji: ${moment(response.data.end_date * 1000).locale('pl').format('LL')}`,
									type: 'success',
									timeout: 10000,
								})
							}
						})
					} else {
						this.addAutoDismissableAlert({
							text: 'Coś poszło nie tak :( Spróbuj jeszcze raz!',
							type: 'error',
						})
					}
				})
			}
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
		onPresetStartDateChange(payload) {
			return this.startDate = payload[0]
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
