<template lang="html">
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ $t('user.passwordResetHeader') }}
				</div>
			</div>
		</div>

		<wnl-form class="margin vertical" name="MyPassword" method="put" resourceRoute="users/current/password">
			<wnl-form-text name="old_password">Stare hasło</wnl-form-text>
			<wnl-form-text name="new_password">Nowe hasło</wnl-form-text>
			<wnl-form-text name="new_password_confirmation">Powtórz nowe hasło</wnl-form-text>
		</wnl-form>
	</div>
</template>

<script>
	import { Form, Text } from 'js/components/global/form'

	export default {
		components: {
			'wnl-form': Form,
			'wnl-form-text': Text,
		},
	}
</script>
