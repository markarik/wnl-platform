<template lang="html">
	<div class="scrollable-main-container wnl-user-profile" :class="{mobile: isMobileProfile}">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ $t('user.myProfile.publicProfile') }}
				</div>
			</div>
			<div class="level-right">
				<span>
					<router-link class="link" :to="{ name: 'user', params: { userId: currentUserId }}" :event="handleLink">
						<a class="my-profile-preview-button button is-primary is-outlined is-small" :disabled="hasChanges">{{ $t('user.myProfile.previewYourProfileButton') }}</a>
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
			<wnl-form-text name="first_name">{{ $t('user.myProfile.first_name') }}</wnl-form-text>
			<wnl-form-text name="last_name">{{ $t('user.myProfile.last_name') }}</wnl-form-text>
			<wnl-form-text name="username">{{ $t('user.myProfile.username') }}</wnl-form-text>
			<wnl-form-text name="city">{{ $t('user.myProfile.city') }}</wnl-form-text>
			<wnl-form-text name="university">{{ $t('user.myProfile.university') }}</wnl-form-text>
			<wnl-form-text name="specialization">{{ $t('user.myProfile.specialization') }}</wnl-form-text>
			<wnl-form-text name="help">{{ $t('user.myProfile.help') }}</wnl-form-text>
			<wnl-form-text name="interests">{{ $t('user.myProfile.interests') }}</wnl-form-text>
			<wnl-form-text name="learning_location">{{ $t('user.myProfile.learning_location') }}</wnl-form-text>
			<wnl-form-text name="about">{{ $t('user.myProfile.about') }}</wnl-form-text>
			<wnl-form-text name="public_email">{{ $t('user.myProfile.public_email') }}</wnl-form-text>
			<wnl-form-text name="public_phone">{{ $t('user.myProfile.public_phone') }}</wnl-form-text>
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
