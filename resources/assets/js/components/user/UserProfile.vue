<template lang="html">
	<div class="scrollable-main-container wnl-user-profile" :class="{mobile: isMobileProfile}">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Profil KOSMICZNY
				</div>
			</div>
		</div>
		<div class="wnl-user-profile-avatar">
			<div class="margin vertical">
				<label class="label">Avatar</label>
			</div>
				<wnl-avatar
				:fullName="this.response.data.full_name"
                :url="this.response.data.avatar"
                class="image is-128x128" size="large"></wnl-avatar>
				<hr>
		</div>

		<wnl-form :hideDefaultSubmit="hideDefaultSubmit" class="margin vertical" name="UserProfile" :resourceRoute="computedResourceRoute" populate="true">
			<wnl-form-text :disableInput="disableInput" name="first_name">Imię</wnl-form-text>
			<wnl-form-text :disableInput="disableInput" name="last_name">Nazwisko</wnl-form-text>
			<wnl-form-text :disableInput="disableInput" name="username">Nazwa użytkownika</wnl-form-text>
			<wnl-form-text :disableInput="disableInput" name="public_email">Adres e-mail</wnl-form-text>
			<wnl-form-text :disableInput="disableInput" name="public_phone">Numer telefonu</wnl-form-text>
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
				align-items: center
				display: flex
				flex-direction: column
				margin-top: 12px

			.button
				margin-top: 20px
</style>

<script>
	import { mapActions, mapGetters } from 'vuex'

    import Avatar from 'js/components/global/Avatar'
	import Upload from 'js/components/global/Upload'
	import { Form, Text } from 'js/components/global/form'
	import { isProduction } from 'js/utils/env'

	export default {
		name: 'UserProfile',
		components: {
            'wnl-avatar': Avatar,
			'wnl-form': Form,
			'wnl-form-text': Text,
			'wnl-upload': Upload,
		},
		props: ['response'],
		data() {
			return {
				loading: false,
				hideDefaultSubmit: true,
				id: this.$route.params.userId,
				disableInput: true,
			}
		},
		computed: {
			...mapGetters(['isMobileProfile']),
			isProduction() {
				return isProduction()
			},
			computedResourceRoute() {
				return `users/${this.id}/profile`
			}
		},
	}
</script>
