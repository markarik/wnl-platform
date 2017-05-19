<template lang="html">
	<div>
		<h1>Twój profil publiczny</h1>
		<wnl-avatar size="large"></wnl-avatar>
		<wnl-upload @success="onUploadSuccess">
			<a>Zmień</a>
		</wnl-upload>

		<wnl-form name="MyProfile" method="put" :resourceUrl="resourceApiUrl" populate="true">
			<wnl-text name="first_name">Imię</wnl-text>
			<wnl-text name="last_name">Nazwisko</wnl-text>
			<wnl-text name="username">Nazwa użytkownika</wnl-text>
			<wnl-text name="public_email">Adres e-mail</wnl-text>
			<wnl-text name="public_phone">Numer telefonu</wnl-text>

			<span slot="submit-after">Zapisz</span>
		</wnl-form>
	</div>
</template>

<style lang="sass">

</style>

<script>
	import { mapActions } from 'vuex'

	import Text from '../global/form/Text'
	import Upload from '../global/Upload'
	import FormComponent from 'js/components/global/form/FormComponent.vue'

	import { getApiUrl } from 'js/utils/env'

	export default {
		name: 'MyProfile',
		components: {
			'wnl-form': FormComponent,
			'wnl-text': Text,
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
