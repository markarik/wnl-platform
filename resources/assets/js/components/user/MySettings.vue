<template lang="html">
	<div class="scrollable-main-container">
		<h1>Ustawienia</h1>

		<wnl-form class="margin vertical"
				  name="Settings"
				  method="put"
				  resourceRoute="users/current/settings"
				  populate="true"
				  hideDefaultSubmit="true">
			<wnl-form-check name="newsletter">Otrzymuj newsletter</wnl-form-check>
			<wnl-form-check name="notify_live">Otrzymuj powiadomienia w przeglądarce</wnl-form-check>
			<wnl-form-check name="chat_on">Chat włączony</wnl-form-check>
			<wnl-form-check name="skip_functional_slides">Pomijaj slajdy funkcjonalne</wnl-form-check>
		</wnl-form>
	</div>
</template>

<style lang="sass">
</style>

<script>
	import {Form, Check, Text} from 'js/components/global/form'
	import Toggler from 'js/components/global/Toggler'
	export default {
		components: {
			'wnl-form': Form,
			'wnl-form-check': Check,
			'wnl-toggler': Toggler,
			'wnl-form-text': Text,
		},
	}
</script>
