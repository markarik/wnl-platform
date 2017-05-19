<template lang="html">
	<div class="scrollable-main-container">
		<h1>Profil publiczny</h1>
		<wnl-avatar size="large"></wnl-avatar>
		<wnl-upload @success="onUploadSuccess">
			<a>Zmień</a>
		</wnl-upload>

		<wnl-form name="MyProfile" method="put" resourceRoute="users/current/profile" populate="true">
			<wnl-form-text name="first_name">Imię</wnl-form-text>
			<wnl-form-text name="last_name">Nazwisko</wnl-form-text>
			<wnl-form-text name="username">Nazwa użytkownika</wnl-form-text>
			<wnl-form-text name="public_email">Adres e-mail</wnl-form-text>
			<wnl-form-text name="public_phone">Numer telefonu</wnl-form-text>
		</wnl-form>
	</div>
</template>

<style lang="sass">

</style>

<script>
	import { mapActions } from 'vuex'

	import { Form, Text } from 'js/components/global/form'
	import Upload from 'js/components/global/Upload'

	export default {
		name: 'MyProfile',
		components: {
			'wnl-form': Form,
			'wnl-form-text': Text,
			'wnl-upload': Upload,
		},
		methods: {
			...mapActions(['setupCurrentUser']),
			onUploadSuccess() {
				this.setupCurrentUser()
			}
		},
	}
</script>
