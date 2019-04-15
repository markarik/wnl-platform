<template>
	<div>
		<wnl-text-overlay :is-loading="isLoading" :text="$t('lessonsAvailability.loader')"/>
		<div class="all-lessons-view">
			<div class="level-left all-lessons-annotation-header">
				<div class="level">
					{{$t('lessonsAvailability.allLessonsAnnotation.header')}}
				</div>
			</div>
			<div class="level-left all-lessons-annotation-explanation">
				<div class="level">
					{{$t('lessonsAvailability.allLessonsAnnotation.explanation')}}
				</div>
			</div>
			<div class="manual-start-dates" v-if="manualStartDates.length > 0">
				<div class="level-left">
					<div class="level-item">
						{{$t('lessonsAvailability.lessonsToBeChangedList')}}
					</div>
				</div>
				<table class="table is-fullwidth">
					<tr>
						<th>Lekcja</th>
						<th>Stara data</th>
						<th>Nowa data</th>
					</tr>
					<tr v-for="(manualStartDate, index) in sortedManualStartDates" :key="index">
						<td>{{manualStartDate.lessonName}}</td>
						<td>{{manualStartDate.oldDate}}</td>
						<td>{{manualStartDate.formatedStartDate}}</td>
					</tr>
				</table>
				<div class="accept-plan">
					<a
						@click="acceptPlan"
						class="button button is-primary is-outlined is-big"
					>{{$t('lessonsAvailability.buttons.acceptPlan')}}
					</a>
				</div>
			</div>
			<div class="level-left">
				<div class="level-item big strong margin bottom">
					{{$t('lessonsAvailability.viewsDropdownInfo')}}
				</div>
			</div>
			<wnl-manual-plan-nodes-list
				:manual-start-dates="manualStartDates"
				:nodes="rootNodes"
				@change="onStartDateChange"
			/>

			<div class="manual-start-dates" v-show="manualStartDates.length > 0">
				<div class="level-left">
					<div class="level-item">
						{{$t('lessonsAvailability.lessonsToBeChangedList')}}
					</div>
				</div>
				<table class="table is-fullwidth">
					<tr>
						<th>Lekcja</th>
						<th>Stara data</th>
						<th>Nowa data</th>
					</tr>
					<tr v-for="(manualStartDate, index) in sortedManualStartDates" :key="index">
						<td>{{manualStartDate.lessonName}}</td>
						<td>{{manualStartDate.oldDate}}</td>
						<td>{{manualStartDate.formatedStartDate}}</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="accept-plan">
			<a
				@click="acceptPlan"
				class="button button is-primary is-outlined is-big"
			>{{$t('lessonsAvailability.buttons.acceptPlan')}}
			</a>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.manual-start-dates
		margin-bottom: $margin-small

	.accept-plan
		display: flex
		justify-content: space-around
		margin-bottom: $margin-small

	.all-lessons-view
		margin-bottom: $margin-base
		width: 100%
		.all-lessons-annotation-header
			width: 100%
			margin-bottom: $margin-base
			overflow-wrap: wrap
			.level-item
				width: 100%
		.all-lessons-annotation-explanation
			width: 100%
			margin-bottom: $margin-base
			overflow-wrap: wrap
			.level-item
				width: 100%

	.accept-plan
		display: flex
		justify-content: space-around
		margin-bottom: $margin-small

</style>

<script>
import axios from 'axios';
import WnlTextOverlay from 'js/components/global/TextOverlay.vue';
import { mapGetters, mapActions } from 'vuex';
import moment from 'moment';
import { getApiUrl } from 'js/utils/env';
import momentTimezone from 'moment-timezone';
import { isEmpty } from 'lodash';
import emits_events from 'js/mixins/emits-events';
import features from 'js/consts/events_map/features.json';
import WnlManualPlanNodesList from 'js/components/user/plan/ManualPlanNodesList';
import { swalConfig } from 'js/utils/swal';

export default {
	name: 'ManualPlan',
	components: {
		WnlTextOverlay,
		WnlManualPlanNodesList,
	},
	mixins: [emits_events],
	data() {
		return {
			manualStartDates: [],
			isLoading: false,
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
		...mapGetters('course', [
			'getChildrenNodes'
		]),
		...mapGetters(['currentUserId']),
		rootNodes() {
			return this.getChildrenNodes(null);
		},
		sortedManualStartDates() {
			return this.manualStartDates
				.slice()
				.sort((a, b) => {
					const dateA = new Date(a.startDate);
					const dateB = new Date(b.startDate);
					return dateA - dateB;
				});
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('course', ['setStructure']),
		onStartDateChange({ newStartDate, lesson }) {
			if (!newStartDate[0]) return;

			const lessonWithStartDate = {
				lessonId: lesson.id,
				lessonName: lesson.name,
				oldDate: moment(lesson.startDate * 1000).format('LL'),
				startDate: newStartDate[0],
				formatedStartDate: moment(newStartDate[0]).format('LL'),
			};

			const index = this.manualStartDates.findIndex((el) => {
				return el.lessonId === lessonWithStartDate.lessonId;
			});

			if (index === -1) {
				this.manualStartDates.push(lessonWithStartDate);
			} else {
				this.manualStartDates.splice(index, 1, lessonWithStartDate);
			}
		},
		async acceptPlan() {
			if (!this.validate()) {
				return false;
			}

			this.$swal(swalConfig({
				title: 'Zmień plan pracy',
				confirmButtonText: 'Akceptuję plan',
				cancelButtonText: 'Zamknij',
				text: 'Czy na pewno chcesz zmienić swój plan pracy?',
				showCancelButton: true,
				type: 'info',
				reverseButtons: true,
			})).then(async () => {
				this.isLoading = true;
				try {
					await axios.put(getApiUrl(`user_lesson/${this.currentUserId}/batch`), {
						manual_start_dates: this.manualStartDates,
						timezone: momentTimezone.tz.guess(),
					});
					await this.setStructure();
					this.isLoading = false;
					this.manualStartDates = [];
					this.addAutoDismissableAlert(this.alertSuccess);
					this.emitUserEvent({
						action: features.manual_settings.actions.save_plan.value,
						feature: features.manual_settings.value,
					});
				}
				catch(error) {
					this.isLoading = false;
					this.manualStartDates = [];
					$wnl.logger.capture(error);
					this.addAutoDismissableAlert(this.alertError);
				}
			}).catch(() => {
				// ignore cancellation of swal
			});
		},
		validate() {
			if (isEmpty(this.manualStartDates)) {
				this.addAutoDismissableAlert({
					text: 'Aby ustawić plan, należy zmienić chociaż jedną datę! :)',
					type: 'error',
					timeout: 3000,
				});
				return false;
			}
			return true;
		}
	}
};
</script>
