<template lang="html">
	<div>
		<h1>Profil publiczny</h1>
		<wnl-avatar size="large"></wnl-avatar>
		<wnl-upload @success="onUploadSuccess">
			<a>Zmień</a>
		</wnl-upload>

		<wnl-form name="MyProfile" method="put" :resourceUrl="resourceApiUrl" populate="true">
			<wnl-form-text name="first_name">Imię</wnl-form-text>
			<wnl-form-text name="last_name">Nazwisko</wnl-form-text>
			<wnl-form-text name="username">Nazwa użytkownika</wnl-form-text>
			<wnl-form-text name="public_email">Adres e-mail</wnl-form-text>
			<wnl-form-text name="public_phone">Numer telefonu</wnl-form-text>

			<span slot="submit-after">Zapisz</span>
		</wnl-form>
	</div>
</template>

<style lang="sass">

</style>

<script>
	import { mapActions } from 'vuex'

	import { Form, Text } from 'js/components/global/form'
	import Upload from 'js/components/global/Upload'

	import { getApiUrl } from 'js/utils/env'

	export default {
		name: 'MyProfile',
		components: {
			'wnl-form': Form,
			'wnl-form-text': Text,
			'wnl-upload': Upload,
		},
		computed: {
			resourceApiUrl() {
				return getApiUrl('users/current/profile')
			}
		},
		methods: {
			...mapActions(['setupCurrentUser']),
			onUploadSuccess() {
				this.setupCurrentUser()
			}
		},
	}
</script>
