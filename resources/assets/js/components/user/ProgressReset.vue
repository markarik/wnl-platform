<template>
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ $t('user.progressReset.header') }}
				</div>
			</div>
		</div>

		<div class="message is-danger reset-container">
			<div class="message-header">
					<strong v-t="'user.progressReset.progressHeader'"></strong>
				</div>
				<div class="message-body" v-t="'progress.reset.info'"></div>
			<button
				@click="resetProgress"
				class="button is-danger to-right"
				v-t="'user.progressReset.progressButton'"/>
		</div>

		<div class="message is-danger reset-container">
			<div class="message-header">
					<strong v-t="'user.progressReset.questionsHeader'"/>
				</div>
			<div class="message-body" v-t="'user.progressReset.questionsWarning'"/>
			<button
				@click="resetQuestions"
				class="button is-danger to-right"
				v-t="'user.progressReset.questionsButton'"/>
		</div>

		<div class="message is-danger reset-container">
			<div class="message-header">
					<strong v-t="'user.progressReset.collectionsHeader'"></strong>
				</div>
			<div class="message-body" v-t="'user.progressReset.collectionsWarning'"/>
			<button
				@click="resetCollections"
				class="button is-danger to-right"
				v-t="'user.progressReset.questionsButton'"/>
		</div>
	</div>

</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.reset-container
		text-align: center
		padding-bottom: $margin-base
		margin-bottom: $margin-huge

		.message-body
			border: none


</style>

<script>
import { swalConfig } from 'js/utils/swal';
import { mapActions, mapGetters } from 'vuex';
import emits_events from 'js/mixins/emits-events';
import context from 'js/consts/events_map/context.json';
import features from 'js/consts/events_map/features.json';

export default {
	mixins: [emits_events],
	methods: {
		...mapActions(['toggleOverlay']),
		...mapActions('progress', ['deleteProgress', 'setupCourse']),
		...mapActions('questions', {deleteQuestions: 'deleteProgress'}),
		...mapActions('collections', ['deleteCollection']),
		...mapActions(['addAutoDismissableAlert']),
		confirmAndExecute(title, text, action) {
			return this.$swal(swalConfig({
				title,
				text,
				showCancelButton: true,
				confirmButtonText: this.$t('ui.confirm.confirm'),
				cancelButtonText: this.$t('ui.confirm.cancel'),
				type: 'error',
				confirmButtonClass: 'button is-danger',
				reverseButtons: true
			}))
				.then(action)
				.then(() => {
					this.addAutoDismissableAlert({
						text: this.$t('user.progressReset.alertSuccess'),
						type: 'success',
						timeout: 10000,
					});
				})
				.catch(error => {
					if (error === 'cancel'  || error === 'overlay') {
						return;
					}
					$wnl.logger.capture(error);
					this.addAutoDismissableAlert({
						text: this.$t('user.progressReset.alertError'),
						type: 'error',
						timeout: 4000,
					});
				});
		},
		resetAndReloadProgress() {
			return Promise.all([this.deleteProgress(), this.setupCourse()]);
		},
		resetProgress() {
			this.confirmAndExecute(
				this.$t('user.progressReset.progressHeader'),
				this.$t('user.progressReset.progressConfirmation'),
				() => {
					this.resetAndReloadProgress();
					this.emitUserEvent({
						subcontext: context.account.subcontext.progress_eraser.value,
						features: features.progress.value,
						action: features.progress.actions.erase_progress.value
					});
				}
			);
		},
		resetQuestions() {
			this.confirmAndExecute(
				this.$t('user.progressReset.questionsHeader'),
				this.$t('user.progressReset.questionsConfirmation'),
				() => {
					this.deleteQuestions();
					this.emitUserEvent({
						subcontext: context.account.subcontext.progress_eraser.value,
						features: features.progress.value,
						action: features.progress.actions.erase_quiz.value
					});
				}
			);
		},
		resetCollections() {
			this.confirmAndExecute(
				this.$t('user.progressReset.collectionsHeader'),
				this.$t('user.progressReset.collectionsConfirmation'),
				() => {
					this.deleteCollection();
					this.emitUserEvent({
						subcontext: context.account.subcontext.progress_eraser.value,
						features: features.progress.value,
						action: features.progress.actions.erase_collections.value
					});
				}
			);
		}
	}
};
</script>
