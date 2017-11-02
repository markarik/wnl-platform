<template lang="html">
	<div class="scrollable-main-container wnl-user-profile" :class="{mobile: isMobileProfile}">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Profil publiczny
				</div>
			</div>
			<div class="level-right">
				<span>
					<router-link class="link" :to="{ name: 'user', params: { userId: currentUserId }}" :event="handleLink">
						<a class="my-profile-preview-button button is-primary is-outlined is-small" :disabled="hasChanges">Zobacz swój profil</a>
					</router-link>
				</span>
			</div>
		</div>
		<div class="wnl-user-profile-avatar">
			<div class="margin vertical">
				<label class="label">Avatar</label>
			</div>
			<wnl-upload
					@uploadStarted="onUploadStarted"
					@success="onUploadSuccess"
					@uploadError="onUploadError"
					endpoint="users/current/avatar"
			>
				<wnl-avatar size="extralarge" class="clickable-avatar"></wnl-avatar>
				<a class="button is-small is-outlined is-primary margin top" :class="{'is-loading': loading}">
					Zmień avatar
				</a>
			</wnl-upload>
		</div>

		<wnl-form class="margin vertical" name="MyProfile" method="put" resourceRoute="users/current/profile" populate="true" ref="form" @formIsLoaded="onFormLoaded">
			<wnl-form-text name="first_name">Imię</wnl-form-text>
			<wnl-form-text name="last_name">Nazwisko</wnl-form-text>
			<wnl-form-text name="username">Nazwa użytkownika</wnl-form-text>
			<wnl-form-text name="city">Miasto</wnl-form-text>
			<wnl-form-text name="university">Uniwersytet</wnl-form-text>
			<wnl-form-text name="specialization">Specka</wnl-form-text>
			<wnl-form-text name="help">W czym mogę pomóc?</wnl-form-text>
			<wnl-form-text name="interests">Zainteresowania</wnl-form-text>
			<wnl-form-text name="learning_location">Gdzie się uczę?</wnl-form-text>
			<wnl-form-text name="about">O</wnl-form-text>
			<wnl-form-text name="public_email">Adres e-mail</wnl-form-text>
			<wnl-form-text name="public_phone">Numer telefonu</wnl-form-text>
		</wnl-form>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-user-profile
		&.mobile
			h1
				text-align: center

			.wnl-upload,
			.wnl-user-profile-avatar
				align-items: center
				display: flex
				flex-direction: column
				margin-top: $margin-medium

			.button
				margin-top: $margin-base

			form
				padding: 0 5%

		.clickable-avatar
			cursor: pointer

		.my-profile-preview-button
			margin-bottom: $margin-small
</style>

<script>
	import { mapActions, mapGetters } from 'vuex'
	import moment from 'moment'

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
		data() {
			return {
				loading: false,
				formLoaded: false,
			}
		},
		computed: {
			...mapGetters(['isMobileProfile', 'currentUserId']),
			isProduction() {
				return isProduction()
			},
			hasChanges() {
				return this.formLoaded && this.getter('hasChanges')
			},
			handleLink() {
				return this.hasChanges ? '' : 'click'
			},
		},
		methods: {
			...mapActions(['updateCurrentUser']),
			onFormLoaded() {
				this.formLoaded = true
			},
			getter(getter) {
				return this.$store.getters[`MyProfile/${getter}`]
			},
			onUploadError() {
				this.loading = false
			},
			onUploadStarted() {
				this.loading = true
			},
			onUploadSuccess(userData) {
				this.updateCurrentUser(userData)
				this.loading = false
			},
		},
	}
</script>
