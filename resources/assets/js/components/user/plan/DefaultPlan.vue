<template>
	<div>
		<wnl-text-overlay :is-loading="isLoading" :text="$t('lessonsAvailability.loader')" />
		<div class="default-plan">
			<div class="level">
				<div class="level-item">
					{{$t('lessonsAvailability.defaultPlan.header')}}
				</div>
			</div>
			<div class="level">
				<div class="level-item">
					{{$t('lessonsAvailability.defaultPlan.annotation')}}
				</div>
			</div>
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

	.default-plan
		margin-bottom: $margin-base
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

export default {
	name: 'DefaultPlan',
	components: {
		'wnl-text-overlay': TextOverlay,
	},
	mixins: [emits_events],
	data() {
		return {
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
		...mapGetters(['currentUserId']),
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('course', ['setStructure']),
		async acceptPlan() {
			this.isLoading = true;
			try {
				await axios.put(getApiUrl(`user_lesson/${this.currentUserId}`), {
					preset_active: 'default',
					timezone: momentTimezone.tz.guess(),
				});
				await this.setStructure();
				this.isLoading = false;
				this.addAutoDismissableAlert(this.alertSuccess);
				this.emitUserEvent({
					action: features.default.actions.restore.value,
					feature: features.default.value,
				});
			}
			catch(error) {
				this.isLoading = false;
				$wnl.logger.capture(error);
				this.addAutoDismissableAlert(this.alertError);
			}
		}
	}
};
</script>
