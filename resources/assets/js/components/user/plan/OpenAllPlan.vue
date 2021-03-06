<template>
	<div>
		<wnl-text-overlay :is-loading="isLoading" :text="$t('lessonsAvailability.loader')" />
		<div class="open-all">
			<div class="level">
				{{$t('lessonsAvailability.openAllLessons.annotation')}}
			</div>
			<div class="level">
				{{$t('lessonsAvailability.openAllLessons.paragraphAnnotation')}}
				{{completedLessonsLength}}/{{availableLength}}.
				wyświetli się: {{completedLessonsLength}}/{{requiredLength}}.
			</div>
			<span>{{$t('lessonsAvailability.openAllLessons.paragraphExplanation')}}</span>
		</div>
		<div class="accept-plan">
			<a
				class="button button is-primary is-outlined is-big"
				@click="acceptPlan"
			>{{$t('lessonsAvailability.buttons.acceptPlan')}}
			</a>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.open-all
		margin-bottom: $margin-base
		width: 100%
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
import TextOverlay from 'js/components/global/TextOverlay.vue';
import { mapGetters, mapActions } from 'vuex';
import { getApiUrl } from 'js/utils/env';
import momentTimezone from 'moment-timezone';
import emits_events from 'js/mixins/emits-events';
import features from 'js/consts/events_map/features.json';
import { swalConfig } from 'js/utils/swal';

export default {
	name: 'OpenAllPlan',
	components: {
		'wnl-text-overlay': TextOverlay,
	},
	mixins: [emits_events],
	data() {
		return {
			satisfactionGuaranteeModalVisible: false,
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
			'getLessons',
			'getRequiredLessons',
		]),
		...mapGetters('progress', ['getCompleteLessons']),
		...mapGetters(['currentUserId']),
		availableLength() {
			return this.getLessons.filter(lesson => lesson.isAvailable && lesson.is_required).length;
		},
		requiredLength() {
			return this.getRequiredLessons.length;
		},
		completedLessonsLength() {
			return this.completedLessons.length;
		},
		completedLessons() {
			return this.getCompleteLessons(1).map(lesson => lesson.id);
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('course', ['setStructure']),
		async acceptPlan() {
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
					await axios.put(getApiUrl(`user_lesson/${this.currentUserId}`), {
						work_load: 0,
						timezone: momentTimezone.tz.guess(),
						preset_active: 'openAll',
					});
					await this.setStructure();
					this.addAutoDismissableAlert(this.alertSuccess);
					this.isLoading = false;
					this.emitUserEvent({
						feature: features.open_all.value,
						action: features.open_all.actions.save_plan.value
					});
				} catch (error) {
					this.isLoading = false;
					$wnl.logger.capture(error);
					this.addAutoDismissableAlert(this.alertError);
				}
			}).catch(() => {
				// ignore swal cancellation
			});
		}
	}
};
</script>
