<template lang="html">
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ $t('user.passwordResetHeader') }}
				</div>
			</div>
		</div>

		<wnl-form
			class="margin vertical"
			name="MyPassword"
			method="put"
			resourceRoute="users/current/password"
			@submitError="submitError"
			:submitError="true">
			<wnl-form-text name="old_password">Stare hasło</wnl-form-text>
			<wnl-form-text name="new_password">Nowe hasło</wnl-form-text>
			<wnl-form-text name="new_password_confirmation">Powtórz nowe hasło</wnl-form-text>
		</wnl-form>
	</div>
</template>

<script>
	import { mapActions } from 'vuex'
	import { Form, Text } from 'js/components/global/form'

	export default {
		components: {
			'wnl-form': Form,
			'wnl-form-text': Text,
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			submitError(payload) {
				if (payload.status === 400) {
					this.addAutoDismissableAlert({
						text: this.$t('ui.error.wrongOldPassword'),
						type: 'error'
					})
				} else {
					this.addAutoDismissableAlert({
						text: this.$t('ui.error.defaultErrorHandle'),
						type: 'error'
					})
				}
			}
		}
	}
</script>
