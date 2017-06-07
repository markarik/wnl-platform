<template lang="html">
	<div class="scrollable-main-container wnl-user-profile" v-bind:class="{mobile: isMobileProfile}">
		<h1>Profil publiczny</h1>
		<div v-if="!isProduction" class="wnl-user-profile-avatar">
			<wnl-avatar size="large"></wnl-avatar>
			<wnl-upload @success="onUploadSuccess">
				<a>Zmień</a>
			</wnl-upload>
		</div>

		<wnl-form class="margin vertical" name="MyProfile" method="put" resourceRoute="users/current/profile" populate="true">
			<wnl-form-text name="first_name">Imię</wnl-form-text>
			<wnl-form-text name="last_name">Nazwisko</wnl-form-text>
			<wnl-form-text name="username">Nazwa użytkownika</wnl-form-text>
			<wnl-form-text name="public_email">Adres e-mail</wnl-form-text>
			<wnl-form-text name="public_phone">Numer telefonu</wnl-form-text>
		</wnl-form>
	</div>
</template>

<style lang="sass">
	.wnl-user-profile
		&.mobile
			h1
				text-align: center

			.wnl-upload,
			.wnl-user-profile-avatar
				margin-top: 12px

			.wnl-user-profile-avatar
				align-items: center
				display: flex
				flex-direction: column

			.button
				margin-top: 20px

			form
				padding: 0 5%

</style>

<script>
	import { mapActions, mapGetters } from 'vuex'

	import Upload from 'js/components/global/Upload'
	import { Form, Text } from 'js/components/global/form'
	import { isProduction } from 'js/utils/env'

	export default {
		name: 'MyProfile',
		components: {
			'wnl-form': Form,
			'wnl-form-text': Text,
			'wnl-upload': Upload,
		},
		computed: {
			...mapGetters(['isMobileProfile']),
			isProduction() {
				return isProduction()
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
