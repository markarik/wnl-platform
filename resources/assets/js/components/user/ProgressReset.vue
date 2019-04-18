<template>
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{$t('user.progressReset.header')}}
				</div>
			</div>
		</div>

		<div class="message is-danger reset-container">
			<div class="message-header">
				<strong v-t="'user.progressReset.progressHeader'" />
			</div>
			<div v-t="'progress.reset.info'" class="message-body" />
			<button
				v-t="'user.progressReset.progressButton'"
				class="button is-danger to-right"
				@click="resetProgress"
			/>
		</div>

		<div class="message is-danger reset-container">
			<div class="message-header">
				<strong v-t="'user.progressReset.questionsHeader'" />
			</div>
			<div v-t="'user.progressReset.questionsWarning'" class="message-body" />
			<button
				v-t="'user.progressReset.questionsButton'"
				class="button is-danger to-right"
				@click="resetQuestions"
			/>
		</div>

		<div class="message is-danger reset-container">
			<div class="message-header">
				<strong v-t="'user.progressReset.collectionsHeader'" />
			</div>
			<div v-t="'user.progressReset.collectionsWarning'" class="message-body" />
			<button
				v-t="'user.progressReset.questionsButton'"
				class="button is-danger to-right"
				@click="resetCollections"
			/>
		</div>
		<wnl-satisfaction-guarantee-modal
			:visible="satisfactionGuaranteeModalVisible"
			@closeModal="satisfactionGuaranteeModalVisible = false"
			@submit="satisfactionGuaranteeModalSubmitAction"
		>
			<template slot="title">{{satisfactionGuaranteeModalTitle}}</template>
		</wnl-satisfaction-guarantee-modal>
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
import { noop } from 'lodash';
import { swalConfig } from 'js/utils/swal';
import { mapActions } from 'vuex';
import emits_events from 'js/mixins/emits-events';
import context from 'js/consts/events_map/context.json';
import features from 'js/consts/events_map/features.json';
import WnlSatisfactionGuaranteeModal from 'js/components/global/modals/SatisfactionGuaranteeModal';

export default {
	components: { WnlSatisfactionGuaranteeModal },
	mixins: [emits_events],
	data() {
		return {
			satisfactionGuaranteeModalVisible: false,
			satisfactionGuaranteeModalSubmitAction: noop,
			satisfactionGuaranteeModalTitle: ''
		};
	},
	methods: {
		...mapActions(['toggleOverlay']),
		...mapActions('progress', ['deleteProgress', 'setupCourse']),
		...mapActions('questions', { deleteQuestions: 'deleteProgress' }),
		...mapActions('collections', ['deleteCollection']),
		...mapActions(['addAutoDismissableAlert']),
		async resetAndReloadProgress() {
			await this.deleteProgress();
			await this.setupCourse();
		},
		resetProgress() {
			this.satisfactionGuaranteeModalVisible = true;
			this.satisfactionGuaranteeModalTitle = this.$t('user.progressReset.progressModalHeader');
			this.satisfactionGuaranteeModalSubmitAction = async () => {
				try {
					await this.resetAndReloadProgress();
					this.emitUserEvent({
						subcontext: context.account.subcontext.progress_eraser.value,
						features: features.progress.value,
						action: features.progress.actions.erase_progress.value
					});
					this.handleSuccess();
				} catch (e) {
					this.handleError(e);
				} finally {
					this.satisfactionGuaranteeModalVisible = false;
				}
			};
		},
		resetQuestions() {
			this.satisfactionGuaranteeModalVisible = true;
			this.satisfactionGuaranteeModalTitle = this.$t('user.progressReset.questionsModalHeader');
			this.satisfactionGuaranteeModalSubmitAction = () => {
				try {
					this.deleteQuestions();
					this.emitUserEvent({
						subcontext: context.account.subcontext.progress_eraser.value,
						features: features.progress.value,
						action: features.progress.actions.erase_quiz.value
					});
					this.handleSuccess();
				} catch (e) {
					this.handleError(e);
				} finally {
					this.satisfactionGuaranteeModalVisible = false;
				}
			};
		},
		resetCollections() {
			this.$swal(swalConfig({
				title: this.$t('user.progressReset.collectionsHeader'),
				text: this.$t('user.progressReset.collectionsConfirmation'),
				showCancelButton: true,
				confirmButtonText: this.$t('ui.confirm.confirm'),
				cancelButtonText: this.$t('ui.confirm.cancel'),
				type: 'error',
				confirmButtonClass: 'button is-danger',
				reverseButtons: true
			}))
				.then(() => {
					this.deleteCollection();
					this.emitUserEvent({
						subcontext: context.account.subcontext.progress_eraser.value,
						features: features.progress.value,
						action: features.progress.actions.erase_collections.value
					});
				})
				.then(this.handleSuccess)
				.catch(error => {
					if (error === 'cancel'  || error === 'overlay') {
						return;
					}
					this.handleError(error);
				});
		},
		handleSuccess() {
			this.addAutoDismissableAlert({
				text: this.$t('user.progressReset.alertSuccess'),
				type: 'success',
				timeout: 10000,
			});
		},
		handleError(error) {
			$wnl.logger.capture(error);
			this.addAutoDismissableAlert({
				text: this.$t('user.progressReset.alertError'),
				type: 'error',
				timeout: 4000,
			});
		}
	}
};
</script>
