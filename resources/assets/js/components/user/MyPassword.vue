<template lang="html">
	<div class="scrollable-main-container">
		<h1>Zmiana hasła</h1>

		<wnl-form name="MyPassword" method="put" resourceRoute="users/current/password">
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
