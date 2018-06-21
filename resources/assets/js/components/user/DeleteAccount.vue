<template>
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ $t('user.deleteAccount.header') }}
				</div>
			</div>
		</div>
		<div class="message is-danger reset-container">
			<div class="message-header">
					<strong v-t="'user.deleteAccount.warningHeader'"/>
				</div>
			<div class="message-body" v-html="htmlWarning"/>
			<div class="reset-button">
				<button
					@click="confirmAndDelete"
					class="button is-danger to-right"
					v-t="'user.deleteAccount.header'"/>
			</div>
		</div>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.reset-container
		padding-bottom: $margin-base
		margin-bottom: $margin-huge

		.message-body
			border: none

		.reset-button
			text-align: center
</style>

<script>
	import { swalConfig } from 'js/utils/swal'
	import { mapActions } from 'vuex'
	import Password from 'js/components/global/form/Password'

	export default {
		name: 'DeleteAccount',
		components: {
			'wnl-form-password': Password,
		},
		data() {
			return {
				inputValue: '',
				htmlWarning: this.$t('user.deleteAccount.warning'),
			}
		},
		methods: {
			...mapActions(['addAutoDismissableAlert', 'deleteAccount']),
			async confirmAndDelete() {
				try {
					await this.$swal(swalConfig({
						title: this.$t('user.deleteAccount.confirmationHeader'),
						text: this.$t('user.deleteAccount.confirmationWarning'),
						input: 'password',
						inputAttributes: {
							autocomplete: 'off',
						},
						inputPlaceholder: 'Wprowadź hasło, aby usunąć konto',
						showCancelButton: true,
						confirmButtonText: this.$t('ui.confirm.confirm'),
						cancelButtonText: this.$t('ui.confirm.cancel'),
						type: 'error',
						confirmButtonClass: 'button is-danger',
						reverseButtons: true
					})).then(() => {
						this.inputValue = this.$swal.getInput().value
					})
					await this.deleteAccount(this.inputValue)
					await this.addAutoDismissableAlert({
						text: this.$t('user.progressReset.alertSuccess'),
						type: 'success',
						timeout: 10000,
					})
				}
				catch (error) {
					$wnl.logger.capture(error)
					this.handleError(error)
				}
			},
			handleError(error) {
				if (error === 'cancel' || error === 'overlay') {
					return
				}
				if (error.response.data.message === 'wrong_password') {
					return this.addAutoDismissableAlert({
						text: this.$t('ui.error.wrongPassword'),
						type: 'error'
					})
				} else if (error.response.data.message === 'unauthorized') {
					return this.addAutoDismissableAlert({
						text: this.$t('ui.error.unauthorized'),
						type: 'error'
					})
				} else {
					return this.addAutoDismissableAlert({
						text: this.$t('user.progressReset.alertError'),
						type: 'error',
						timeout: 4000,
					})
				}
			}
		}
	}
</script>
