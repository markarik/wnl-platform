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
			<wnl-form-password name="old_password">Stare hasło</wnl-form-password>
			<wnl-form-password name="new_password">Nowe hasło</wnl-form-password>
			<wnl-form-password name="new_password_confirmation">Powtórz nowe hasło</wnl-form-password>
		</wnl-form>
	</div>
</template>

<script>
import { mapActions } from 'vuex';
import Form from 'js/components/global/form/Form';
import Password from 'js/components/global/form/Password';

export default {
	components: {
		'wnl-form': Form,
		'wnl-form-text': Text,
		'wnl-form-password': Password,
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		submitError(payload) {
			if (payload.data.message === 'wrong_old_password') {
				this.addAutoDismissableAlert({
					text: this.$t('ui.error.wrongOldPassword'),
					type: 'error'
				});
			} else if (payload.data.message === 'same_passwords') {
				this.addAutoDismissableAlert({
					text: this.$t('ui.error.samePasswords'),
					type: 'error'
				});
			} else {
				this.addAutoDismissableAlert({
					text: this.$t('ui.error.defaultErrorHandle'),
					type: 'error'
				});
			}
		}
	}
};
</script>
