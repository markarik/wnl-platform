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
			<div class="message-body" v-t="'user.deleteAccount.warning'"/>
			<div class="control">
				<input
					autocomplete="new-password"
					type="password"
					class="input"
					v-model="inputValue"/>
			</div>
			<button
				@click="confirmAndDelete"
				class="button is-danger to-right"
				v-t="'user.deleteAccount.header'"/>
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
			}
		},
		methods: {
			...mapActions(['addAutoDismissableAlert', 'deleteAccount']),
			async confirmAndDelete() {
				try {
					await this.$swal(swalConfig({
						title: this.$t('user.deleteAccount.confirmationHeader'),
						text: this.$t('user.deleteAccount.confirmationWarning'),
						showCancelButton: true,
						confirmButtonText: this.$t('ui.confirm.confirm'),
						cancelButtonText: this.$t('ui.confirm.cancel'),
						type: 'error',
						confirmButtonClass: 'button is-danger',
						reverseButtons: true
					}))
					await this.deleteAccount(this.inputValue)
					await this.addAutoDismissableAlert({
						text: this.$t('user.progressReset.alertSuccess'),
						type: 'success',
						timeout: 10000,
					})
				}
				catch (error) {
					if (error === 'cancel') {
						return
					}
					$wnl.logger.capture(error)
					this.addAutoDismissableAlert({
						text: this.$t('user.progressReset.alertError'),
						type: 'error',
						timeout: 4000,
					})
				}
			}
		}
	}
</script>
