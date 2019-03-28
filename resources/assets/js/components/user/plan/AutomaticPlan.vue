<template>
	<div>
		<wnl-text-overlay :is-loading="isLoading" :text="$t('lessonsAvailability.loader')"/>
		<div class="presets-view">
			<div class="wnl-screen-title">
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
			<div class="wnl-screen-title">
				<div class="level-left">
					<div class="level-item big strong">
						{{ $t('lessonsAvailability.sections.availablePresets') }}
					</div>
				</div>
			</div>
			<div class="presets-control">
				<a v-for="preset in presets"
					 class="panel-toggle preset preset"
					 :class="{'is-active': isPresetActive(preset.preset)}"
					 :key="preset.preset"
					 @click="togglePreset(preset.preset)"
				>{{ $t(preset.title) }}
					<span class="icon is-small">
						<i class="fa"
							 :class="[isPresetActive(preset.preset) ? 'fa-check-circle' : 'fa-circle-o']"></i>
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
						 :key="workLoad.workLoad"
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
				<div class="wnl-screen-title">
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
								:with-border="true"
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
								:with-border="true"
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
								:with-border="true"
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
			<div v-if="showAnnotation">
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
		</div>
		<div class="accept-plan">
			<a
				:disabled="isSubmitDisabled"
				@click="!isSubmitDisabled && (satisfactionGuaranteeModalVisible = true)"
				class="button is-primary is-outlined is-big"
			>{{ $t('lessonsAvailability.buttons.acceptPlan') }}
			</a>
		</div>
		<wnl-satisfaction-guarantee-modal
			:visible="satisfactionGuaranteeModalVisible"
			:title="$t('user.plan.changePlanConfirmation')"
			@closeModal="satisfactionGuaranteeModalVisible = false"
			@submit="acceptPlan"
		></wnl-satisfaction-guarantee-modal>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.days-info
		margin-bottom: $margin-small

	.days
		display: flex
		flex-wrap: wrap
		justify-content: center
		margin-bottom: $margin-big

	.presets-control, .work-load-control
		display: flex
		flex-wrap: wrap
		justify-content: center

	.presets-control
		margin-bottom: $margin-big

	.work-load-control
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


	.accept-plan
		display: flex
		justify-content: space-around
		margin-bottom: $margin-small
</style>

<script>
import {isEmpty} from 'lodash';
import moment from 'moment';
import momentTimezone from 'moment-timezone';
import {mapGetters, mapActions} from 'vuex';

import TextOverlay from 'js/components/global/TextOverlay.vue';
import Datepicker from 'js/components/global/Datepicker';
import WnlSatisfactionGuaranteeModal from 'js/components/global/modals/SatisfactionGuaranteeModal';

import features from 'js/consts/events_map/features.json';
import emits_events from 'js/mixins/emits-events';
import {getApiUrl} from 'js/utils/env';

export default {
	name: 'AutomaticPlan',
	components: {
		'wnl-text-overlay': TextOverlay,
		'wnl-datepicker': Datepicker,
		WnlSatisfactionGuaranteeModal
	},
	mixins: [emits_events],
	props: {
		showAnnotation: {
			type: Boolean,
			default: true,
		},
	},
	data() {
		return {
			satisfactionGuaranteeModalVisible: false,
			isLoading: false,
			isSubmitDisabled: false,
			activePreset: 'dateToDate',
			startDate: new Date(),
			endDate: null,
			workDays: [1, 2, 3, 4, 5],
			workLoad: null,
			defaultDateConfig: {
				altInput: true,
				disableMobile: true,
			},
			presets: [
				{
					title: 'lessonsAvailability.presets.dateToDate',
					preset: 'dateToDate',
				},
				{
					title: 'lessonsAvailability.presets.daysPerLesson',
					preset: 'daysPerLesson',
				}
			],
			days: [
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
			],
			alertSuccess: {
				text: this.$i18n.t('lessonsAvailability.alertSuccess'),
				type: 'success',
			},
			alertError: {
				text: this.$i18n.t('lessonsAvailability.alertError'),
				type: 'error',
			},
		};
	},
	computed: {
		...mapGetters(['currentUserId', 'currentUserSubscriptionDates']),
		...mapGetters('course', [
			'getRequiredLessons',
		]),
		...mapGetters('progress', [
			'getCompleteLessons',
		]),
		inProgressLessonsLength() {
			return Object.keys(this.getRequiredLessons).filter(requiredLesson => {
				return !this.completedLessons.includes(Number(requiredLesson));
			}).length;
		},
		completedLessonsLength() {
			return Object.keys(this.getRequiredLessons).filter(requiredLesson => {
				return this.completedLessons.includes(Number(requiredLesson));
			}).length;
		},
		minimumEndDate() {
			return moment(this.startDate).add(Math.ceil(this.inProgressLessonsLength * 7 / this.workDays.length), 'days').toDate();
		},
		completedLessons() {
			return this.getCompleteLessons(1).map(lesson => lesson.id);
		},
		startDateConfigWithMin() {
			return {
				...this.defaultDateConfig,
				minDate: 'today'
			};
		},
		endDateConfig() {
			return {
				...this.defaultDateConfig,
				defaultDate: this.minimumEndDate,
				minDate: this.minimumEndDate,
			};
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
			];
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('course', ['setStructure']),
		isPresetActive(preset) {
			return this.activePreset === preset;
		},
		isWorkLoadActive(workLoad) {
			return this.workLoad === workLoad;
		},
		onPresetStartDateChange(payload) {
			this.isSubmitDisabled = false;
			return this.startDate = payload[0];
		},
		chooseWorkload(workLoad) {
			this.isSubmitDisabled = false;
			this.workLoad = workLoad;
		},
		onEndDateChange(payload) {
			this.isSubmitDisabled = false;
			if (isEmpty(payload)) this.endDate = null;
		},
		togglePreset(preset) {
			this.isSubmitDisabled = false;
			return this.activePreset = preset;
		},
		isDayActive(dayNumber) {
			return this.workDays.includes(dayNumber);
		},
		toggleDay(dayNumber) {
			this.isSubmitDisabled = false;

			let index = this.workDays.indexOf(dayNumber);
			if (index === -1) {
				return this.workDays.push(dayNumber);
			} else {
				return this.workDays.splice(index, 1);
			}
		},
		async acceptPlan() {
			this.satisfactionGuaranteeModalVisible = false;
			if (this.activePreset === 'dateToDate') {
				this.workLoad = null;
			}

			if (!this.validate()) {
				return false;
			}

			this.isLoading = true;
			try {
				const response = await axios.put(getApiUrl(`user_lesson/${this.currentUserId}`), {
					work_days: this.workDays,
					work_load: this.workLoad,
					start_date: this.startDate,
					end_date: this.endDate,
					timezone: momentTimezone.tz.guess(),
					preset_active: this.activePreset,
				});
				await this.setStructure();
				if (moment(response.data.end_date).isSameOrAfter(moment(this.currentUserSubscriptionDates.max))) {
					this.addAutoDismissableAlert({
						text: `Data otwarcia ostatniej lekcji: ${moment(response.data.end_date * 1000).locale('pl').format('LL')}, wypada poza datą Twojej subskrypcji: ${moment(this.currentUserSubscriptionDates).locale('pl').format('LL')}. Plan został ustalony według Twoich ustawień.`,
						type: 'warning',
						timeout: 10000,
					});
				} else {
					this.addAutoDismissableAlert(this.alertSuccess);
				}
				this.isSubmitDisabled = true;
				this.isLoading = false;
				this.emitUserEvent({
					action: features.automatic_settings.actions.save_plan.value,
					feature: features.automatic_settings.value
				});
			} catch (error) {
				this.isLoading = false;
				$wnl.logger.capture(error);
				this.addAutoDismissableAlert(this.alertError);
			}
		},
		validate() {
			if (isEmpty(this.workDays)) {
				this.addAutoDismissableAlert({
					text: 'Wybierz przynajmniej jeden dzień, w którym chcesz aby otwierały się lekcje :)',
					type: 'error',
					timeout: 3000,
				});
				return false;
			} else if (this.workLoad === null &&
					this.activePreset === 'daysPerLesson') {
				this.addAutoDismissableAlert({
					text: 'Zaznacz, ile dni chcesz poświęcić na jedną lekcję :)',
					type: 'error',
					timeout: 3000,
				});
				return false;
			} else if (this.endDate === null &&
					this.activePreset === 'dateToDate') {
				this.addAutoDismissableAlert({
					text: 'Wybierz datę, w której ma zakończyć się nauka :)',
					type: 'error',
					timeout: 3000,
				});
				return false;
			} else if (this.activePreset === '') {
				this.addAutoDismissableAlert({
					text: 'Wybierz któryś z dostępnych planów nauki :)',
					type: 'error',
					timeout: 3000,
				});
				return false;
			}
			return true;
		},
	},
};
</script>
