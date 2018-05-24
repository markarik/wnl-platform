<template>
	<div>
		<div class="wnl-overlay" v-if="isLoading">
			<span class="loader"></span>
			<span class="loader-text">{{ $t('lessonsAvailability.loader') }}</span>
		</div>
		<article class="message is-info">
			<div class="message-header">
				<p>Twój Plan Pracy</p>
			</div>
			<div class="message-body plan-details">
				<span>Twój obecny plan pracy zakłada naukę od <strong>{{planStartDate}}</strong> do <strong>{{planEndDate}}</strong>.</span>
				<span>Aby podejrzeć daty otwarcia poszczególnych lekcji wejdź w Twój Plan Pracy > Ustaw plan ręcznie.</span>
			</div>
		</article>
		<div class="views-control">
			<a v-for="(name, view) in views"
				 class="panel-toggle view"
				 :class="{'is-active': isViewActive(view)}"
				 :key="view"
				 @click="toggleView(view)"
			>{{ $t(name) }}
				<span class="icon is-small">
					<i class="fa"
						 :class="[isViewActive(view) ? 'fa-check-circle' : 'fa-circle-o']"></i>
				</span>
			</a>
		</div>
		<div class="all-lessons-view"  v-if="activeView === 'lessonsView'">
			<div class="level-left all-lessons-annotation">
				<div class="level-item">
					{{ $t('lessonsAvailability.allLessonsAnnotation')}}
				</div>
			</div>
			<div class="level-left">
				<div class="level-item big strong margin bottom">
					{{ $t('lessonsAvailability.viewsDropdownInfo') }}
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
											@onChange="(payload) => onStartDateChange(payload, subitem)"
										/>
									</div>
								</div>
							</li>
						</ul>
					</li>
				</ul>
			</div>
			<div class="manual-start-dates" v-if="manualStartDates.length > 0">
				<div class="level wnl-screen-title">
					<div class="level-item">
						{{ $t('lessonsAvailability.lessonsToBeChangedList') }}
					</div>
				</div>
				<div class="level wnl-screen-title">
					<div class="level-item">
						<div class="dates-list">
							<div class="date" v-for="manualStartDate in manualStartDates">
								{{ manualStartDate.lessonName }} - {{manualStartDate.formatedStartDate}}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="default-plan" v-if="activeView === 'default'">
			<div class="level">
				<div class="level-item">
					{{ $t('lessonsAvailability.secondSection.defaultPlan')}}
				</div>
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
				<a v-for="day in days"
					 class="panel-toggle day"
					 :class="{'is-active': isDayActive(day.dayNumber)}"
					 :key="day.dayNumber"
					 @click="toggleDay(day.dayNumber)"
				>{{ $t(day.dayName) }}
					<span class="icon is-small">
							<i class="fa"
								 :class="[isDayActive(day.dayNumber) ? 'fa-check-circle' : 'fa-circle-o']"></i>
						</span>
				</a>
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
					 class="panel-toggle preset preset"
					 :class="{'is-active': isPresetActive(preset)}"
					 :key="preset"
					 @click="togglePreset(preset)"
				>{{ $t(name) }}
					<span class="icon is-small">
						<i class="fa"
							 :class="[isPresetActive(preset) ? 'fa-check-circle' : 'fa-circle-o']"></i>
					</span>
				</a>
			</div>
			<div class="work-load-toggle" v-if="isPresetActive('daysPerLesson')">
				<div class="level wnl-screen-title">
					<div class="level-item">
						{{ $t('lessonsAvailability.secondSection.daysPerLesson') }}
					</div>
				</div>
				<div class="work-load-control">
					<a v-for="workLoad in availableWorkLoads"
						 @click="chooseWorkload(workLoad.workLoad)"
						 class="panel-toggle work-load-button"
						 :class="{'is-active': isWorkLoadActive(workLoad.workLoad)}"
					>{{ $t(workLoad.translation) }}
						<span class="icon is-small">
							<i class="fa"
								 :class="[isWorkLoadActive(workLoad.workLoad) ? 'fa-check-circle' : 'fa-circle-o']">
							</i>
						</span>
					</a>
				</div>
				<div class="level wnl-screen-title">
					<div class="level-item">
						{{ $t('lessonsAvailability.secondSection.startDate') }}
					</div>
				</div>
				<div class="dates">
					<div class="date">
						<div class="start-date-picker">
							<label class="date-label">
								{{$t('questions.plan.headings.startDate')}}
								<span class="icon is-small">
									<i class="fa fa-hourglass-1"></i>
								</span>
							</label>
							<wnl-datepicker
								:withBorder="true"
								v-model="startDate"
								:config="startDateConfigWithMin"
								@onChange="onPresetStartDateChange"/>
							<p class="tip">
								{{$t('questions.plan.tips.startDate')}}
							</p>
						</div>
					</div>
				</div>
			</div>
			<div class="" v-if="isPresetActive('dateToDate')">
				<div class="level">
					<div class="level-item">
						{{ $t('lessonsAvailability.secondSection.dateToDate') }}
					</div>
				</div>
				<div class="dates">
					<div class="date">
						<div class="start-date-picker">
							<label class="date-label">
								{{$t('questions.plan.headings.startDate')}}
								<span class="icon is-small">
									<i class="fa fa-hourglass-1"></i>
								</span>
							</label>
							<wnl-datepicker
								:withBorder="true"
								v-model="startDate"
								:config="startDateConfigWithMin"
								@onChange="onPresetStartDateChange"/>
							<p class="tip">
								{{$t('questions.plan.tips.startDate')}}
							</p>
						</div>
					</div>
					<div class="date">
						<div class="end-date-picker">
							<label class="date-label">
								{{$t('questions.plan.headings.endDate')}}
								<span class="icon is-small">
									<i class="fa fa-hourglass-3"></i>
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
			<div class="annotation">
				<div class="level">
					<div class="level-item" v-if="this.completedLessonsLength > 0">
						{{ $t('lessonsAvailability.annotation.header') }}
						{{ this.completedLessonsLength}}{{ $t('lessonsAvailability.annotation.info') }}
					</div>
				</div>
			</div>
		</div>
		<div class="open-all" v-if="activeView === 'openAll'">
			<div class="level">
				<div class="level-item">
					{{ $t('lessonsAvailability.openAllLessons.annotation') }}
				</div>
			</div>
			<div class="level-item">
				{{ $t('lessonsAvailability.openAllLessons.paragraphAnnotation')}}
				{{ this.completedLessonsLength }}/{{ this.availableLength }}.
				wyświetli się: {{ this.completedLessonsLength }}/{{this.requiredLength}}.
			</div>
			<span>{{ $t('lessonsAvailability.openAllLessons.paragraphExplanation')}}</span>
		</div>
		<div class="accept-plan">
			<a
				@click="acceptPlan"
				class="button button is-primary is-outlined is-big"
			>{{ $t('lessonsAvailability.buttons.acceptPlan') }}
			</a>
		</div>
	</div>
</template>

<style lang="sass" scoped>
@import 'resources/assets/sass/variables'

.plan-details
	display: flex
	flex-direction: column

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
		color: $color-ocean-blue
		margin-top: $margin-small

.views-control
	display: flex
	flex-wrap: wrap
	justify-content: center
	margin-bottom: $margin-base

.days-info
	margin-bottom: $margin-small

.days
	display: flex
	flex-wrap: wrap
	justify-content: center
	margin-bottom: $margin-big

.default-plan
	margin-bottom: $margin-base

.presets-control
	display: flex
	flex-wrap: wrap
	justify-content: center
	margin-bottom: $margin-big

.work-load-control
	display: flex
	flex-wrap: wrap
	justify-content: center
	margin-bottom: $margin-base

.dates
	.date
		margin-bottom: $margin-big
		label, .tip
			display: inline-block
			text-align: center
			width: 100%

.annotation
	margin-bottom: $margin-base

.open-all
	margin-bottom: $margin-base
	width: 100%
	text-align: center
	overflow-wrap: wrap
	.level-item
		width: 100%

.manual-start-dates
	margin-bottom: $margin-small

.accept-plan
	display: flex
	justify-content: space-around
	margin-bottom: $margin-small

.all-lessons-view
	margin-bottom: $margin-base
	width: 100%
	.all-lessons-annotation
		width: 100%
		margin-bottom: $margin-base
		overflow-wrap: wrap
		.level-item
			width: 100%

.groups
	.groups-list
		.group
			margin-bottom: $margin-base
			.item-toggle
				color: $color-sky-blue
				cursor: pointer
				text-align: center
				text-transform: uppercase
				margin-bottom: $margin-small
				width: 100%
				.icon
					color: $color-gray
				.subitems-count
					color: $color-background-gray
					font-size: $font-size-minus-2

			.subitems
				display: flex
				flex-direction: column
				margin-bottom: $margin-small
				.subitem
					display: flex
					flex-direction: row-reverse
					justify-content: space-between
					margin-bottom: $margin-small
					margin-top: $margin-small
					min-height: 35px
					&.isEven
						background-color: $color-background-lightest-gray
					.subitem-name
						align-self: flex-end
						color: $color-gray
						width: 65%
						&.is-grayed-out
							color: $color-gray-dimmed
					.subitem-left-side
						align-items: center
						display: flex
						margin-right: $margin-small
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
	import { isEmpty, first, last } from 'lodash'
	import moment from 'moment'
	import momentTimezone from 'moment-timezone'

	export default {
		name: 'LessonsAvailabilities',
		components: {
			'wnl-datepicker': Datepicker,
		},
		data() {
			return {
				isLoading: false,
				openGroups: [],
				availablePresets: ['daysPerLesson', 'dateToDate'],
				activePreset: 'dateToDate',
				startDate: new Date(),
				endDate: null,
				workDays: [1, 2, 3, 4, 5],
				workLoad: null,
				activeView: 'lessonsView',
				manualStartDates: [],
				alertSuccess: {
					text: this.$i18n.t('lessonsAvailability.alertSuccess'),
					type: 'success',
				},
				alertError: {
					text: this.$i18n.t('lessonsAvailability.alertError'),
					type: 'error',
				},
				defaultDateConfig: {
					altInput: true,
					disableMobile: true,
				},
			}
		},
		computed: {
			...mapGetters(['currentUserId', 'currentUserSubscriptionDates', 'isMobile']),
			...mapGetters('course', [
				'name',
				'groups',
				'getRequiredLessons',
				'structure',
				'userLessons',
			]),
			...mapGetters('progress', [
				'getCompleteLessons',
			]),
			availableLength() {
				return this.userLessons.filter(lesson => lesson.isAvailable && lesson.is_required).length
			},
			requiredLength() {
				return this.userLessons.filter(lesson => lesson.is_required).length
			},
			inProgressLessonsLength() {
				return Object.keys(this.getRequiredLessons).filter(requiredLesson => {
					return !this.completedLessons.includes(Number(requiredLesson))
				}).length
			},
			completedLessonsLength() {
				return Object.keys(this.getRequiredLessons).filter(requiredLesson => {
					return this.completedLessons.includes(Number(requiredLesson))
				}).length
			},
			sortedUserLessons() {
				return this.userLessons.sort((lessonA, lessonB) => {
					return lessonA.startDate - lessonB.startDate
				})
			},
			planStartDate() {
				if (!first(this.sortedUserLessons)) return

				return moment(first(this.sortedUserLessons).startDate * 1000).format('LL')
			},
			planEndDate() {
				if (!last(this.sortedUserLessons)) return

				return moment(last(this.sortedUserLessons).startDate * 1000).format('LL')
			},
			minimumEndDate() {
				return moment(this.startDate).add(Math.ceil(this.inProgressLessonsLength * 7 / this.workDays.length), 'days').toDate()
			},
			completedLessons() {
				return this.getCompleteLessons(1).map(lesson => lesson.id)
			},
			startDateConfig() {
				return {
					...this.defaultDateConfig
				}
			},
			startDateConfigWithMin() {
				return {
					...this.defaultDateConfig,
					minDate: 'today'
				}
			},
			endDateConfig() {
				return {
					...this.defaultDateConfig,
					defaultDate: this.minimumEndDate,
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
					dateToDate: 'lessonsAvailability.presets.dateToDate',
					daysPerLesson: 'lessonsAvailability.presets.daysPerLesson',
				}
			},
			views() {
				return {
					lessonsView: 'lessonsAvailability.views.lessonsView',
					default: 'lessonsAvailability.views.default',
					presetsView: 'lessonsAvailability.views.presetsView',
					openAll: 'lessonsAvailability.views.openAll',
				}
			},
			availableWorkLoads() {
				return [
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
					}
				]
			},
			days() {
				return [
					{
						dayName: 'lessonsAvailability.days.monday',
						dayNumber: 1
					},
					{
						dayName: 'lessonsAvailability.days.tuesday',
						dayNumber: 2
					},
					{
						dayName: 'lessonsAvailability.days.wednesday',
						dayNumber: 3
					},
					{
						dayName: 'lessonsAvailability.days.thursday',
						dayNumber: 4
					},
					{
						dayName: 'lessonsAvailability.days.friday',
						dayNumber: 5
					},
					{
						dayName: 'lessonsAvailability.days.saturday',
						dayNumber: 6
					},
					{
						dayName: 'lessonsAvailability.days.sunday',
						dayNumber: 7
					},
				]
			},
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			...mapActions('course', [
				'setLessonAvailabilityStatus',
				'updateLessonStartDate',
				'setStructure'
			]),
			...mapActions(['toggleOverlay']),
			isWorkLoadActive(workLoad) {
				return this.workLoad === workLoad
			},
			isPresetActive(preset) {
				return this.activePreset === preset
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
			toggleView(view) {
				return this.activeView = view
			},
			togglePreset(preset) {
				return this.activePreset = preset
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
			async acceptPlan() {
				if (this.activeView === 'presetView' && this.activePreset === 'dateToDate') {
					this.workLoad = null
				} else if (this.activeView === 'openAll') {
					this.workLoad = null
					this.activePreset = 'openAll'
				} else if (this.activeView === 'default') {
					this.activePreset = 'default'
				}

				if (!this.validate()) {
					return false
				}

				if (this.activeView === 'lessonsView') {
					this.isLoading = true
					try {
						await axios.put(getApiUrl(`user_lesson/${this.currentUserId}/batch`), {
							manual_start_dates: this.manualStartDates,
							timezone: momentTimezone.tz.guess(),
						})
						await this.setStructure()
						this.isLoading = false
						this.manualStartDates = []
						this.addAutoDismissableAlert(this.alertSuccess)
					}
					catch(error) {
						this.isLoading = false
						$wnl.logger.capture(error)
						this.addAutoDismissableAlert(this.alertError)
					}
				} else {
					this.isLoading = true
					try {
						const response = await axios.put(getApiUrl(`user_lesson/${this.currentUserId}`), {
							work_days: this.workDays,
							work_load: this.workLoad,
							start_date: this.startDate,
							end_date: this.endDate,
							timezone: momentTimezone.tz.guess(),
							preset_active: this.activePreset,
						})
						await this.setStructure()
						if (moment(response.data.end_date).isSameOrAfter(moment(this.currentUserSubscriptionDates.max))) {
							this.addAutoDismissableAlert({
								text: `Data otwarcia ostatniej lekcji: ${moment(response.data.end_date * 1000).locale('pl').format('LL')}, wypada poza datą Twojej subskrypcji: ${moment(this.currentUserSubscriptionDates).locale('pl').format('LL')}. Plan został ustalony według Twoich ustawień.`,
								type: 'error',
								timeout: 10000,
							})
						} else {
							this.addAutoDismissableAlert(this.alertSuccess)
						}
						this.isLoading = false
					}
					catch(error) {
						this.isLoading = false
						$wnl.logger.capture(error)
						this.addAutoDismissableAlert(this.alertError)
					}
				}
			},
			validate() {
				if (isEmpty(this.workDays) && !['lessonView', 'default'].includes(this.activeView)) {
					this.addAutoDismissableAlert({
						text: `Wybierz przynajmniej jeden dzień, w którym chcesz aby otwierały się lekcje :)`,
						type: 'error',
						timeout: 3000,
					})
					return false
				} else if (this.workLoad === null && this.activePreset === 'daysPerLesson' && !this.activeView === 'lessonsView') {
					this.addAutoDismissableAlert({
						text: `Zaznacz, ile dni chcesz poświęcić na jedną lekcję :)`,
						type: 'error',
						timeout: 3000,
					})
					return false
				} else if (this.endDate === null &&
					this.activePreset === 'dateToDate' &&
					!this.activeView === 'lessonsView') {
					this.addAutoDismissableAlert({
						text: `Wybierz datę, w której ma zakończyć się nauka :)`,
						type: 'error',
						timeout: 3000,
					})
					return false
				} else if (this.activePreset === '') {
					this.addAutoDismissableAlert({
						text: `Wybierz któryś z dostępnych planów nauki :)`,
						type: 'error',
						timeout: 3000,
					})
					return false
				} else if (this.activePreset === 'lessonsView' &&
					isEmpty(this.manualStartDates)) {
					this.addAutoDismissableAlert({
						text: `Aby ustawić plan, należy zmienić chociaż jedną datę! :)`,
						type: 'error',
						timeout: 3000,
					})
					return false
				}
				return true
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
			sortedManualStartDates() {
				return this.manualStartDates.sort((a, b) => {
					const dateA = new Date(a.startDate)
					const dateB = new Date(b.startDate)
					return dateA - dateB
				})
			},
			onStartDateChange(newStartDate, subitem) {
				if (!newStartDate[0]) return

				const lessonWithStartDate = {
					lessonId: subitem.id,
					lessonName: subitem.name,
					startDate: newStartDate[0],
					formatedStartDate: moment(newStartDate[0]).format('LL'),
				}

				const index = this.manualStartDates.findIndex((el) => {
					return el.lessonId === lessonWithStartDate.lessonId
				})

				if (index === -1) {
					this.manualStartDates.push(lessonWithStartDate)
				} else {
					this.manualStartDates.splice(index, 1, lessonWithStartDate)
				}
				this.sortedManualStartDates()
			}
		}
	}
</script>
