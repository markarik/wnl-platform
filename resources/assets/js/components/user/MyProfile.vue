<template>
	<div class="scrollable-main-container wnl-user-profile" :class="{mobile: isMobileProfile}">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{$t('user.myProfile.publicProfile')}}
				</div>
			</div>
			<div class="level-right preview-button" :class="{mobile: isMobileProfile}">
				<span>
					<router-link
						class="link"
						:to="{ name: 'user', params: { userId: currentUserId }}"
						:event="handleLink"
					>
						<a class="my-profile-preview-button button is-primary is-outlined is-small" :disabled="hasChanges">{{buttonNameToDisplay}}</a>
					</router-link>
				</span>
			</div>
		</div>
		<div class="wnl-user-profile-avatar">
			<div class="margin vertical">
				<label class="label">Avatar</label>
			</div>
			<wnl-upload
				endpoint="users/current/avatar"
				@uploadStarted="onUploadStarted"
				@success="onUploadSuccess"
				@uploadError="onUploadError"
			>
				<wnl-avatar size="extraextralarge" class="clickable-avatar"></wnl-avatar>
				<a class="change-avatar-button button is-small is-outlined is-primary margin top" :class="{'is-loading': loading}">
					Zmień avatar
				</a>
			</wnl-upload>
		</div>

		<wnl-form
			ref="form"
			class="margin vertical"
			name="MyProfile"
			method="put"
			resource-route="users/current/profile"
			populate="true"
			@formIsLoaded="onFormLoaded"
		>
			<div class="form-input-group">
				<wnl-form-text name="help" :placeholder="$t('user.myProfile.helpPlaceholder')">{{$t('user.myProfile.help')}}</wnl-form-text>
				<wnl-form-text name="specialization">{{$t('user.myProfile.specialization')}}</wnl-form-text>
			</div>
			<div class="form-input-group">
				<wnl-form-text name="university">{{$t('user.myProfile.university')}}</wnl-form-text>
				<wnl-form-text name="city">{{$t('user.myProfile.city')}}</wnl-form-text>
				<wnl-form-text name="learning_location">{{$t('user.myProfile.learning_location')}}</wnl-form-text>
			</div>
			<div class="form-input-group">
				<wnl-form-text name="interests">{{$t('user.myProfile.interests')}}</wnl-form-text>
				<wnl-form-text name="about" :placeholder="$t('user.myProfile.aboutPlaceholder')">{{$t('user.myProfile.about')}}</wnl-form-text>
				<wnl-form-text name="public_email">{{$t('user.myProfile.public_email')}}</wnl-form-text>
			</div>
		</wnl-form>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-user-profile
		&.mobile
			h1
				text-align: center

			.preview-button
				&.mobile
					display: flex
					flex-direction: column
					margin-bottom: $margin-big
					padding-left: $margin-base

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
import { mapActions, mapGetters } from 'vuex';

import Upload from 'js/components/global/Upload';
import { Form, Text } from 'js/components/global/form';
import { isProduction } from 'js/utils/env';
import { ALERT_TYPES } from 'js/consts/alert';

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
		};
	},
	computed: {
		...mapGetters(['isMobileProfile', 'currentUserId']),
		isProduction() {
			return isProduction();
		},
		buttonNameToDisplay() {
			return this.formLoaded && this.getter('hasChanges') ? this.$t('user.myProfile.previewYourProfileButtonDisabled') : this.$t('user.myProfile.previewYourProfileButtonEnabled');
		},
		hasChanges() {
			return this.formLoaded && this.getter('hasChanges');
		},
		handleLink() {
			return this.hasChanges ? '' : 'click';
		},
	},
	methods: {
		...mapActions(['updateCurrentUserProfile', 'addAutoDismissableAlert']),
		onFormLoaded() {
			this.formLoaded = true;
		},
		getter(getter) {
			return this.$store.getters[`MyProfile/${getter}`];
		},
		onUploadError(e) {
			this.loading = false;

			$wnl.logger.error(e);
			this.addAutoDismissableAlert({
				text: 'Ups, coś poszło nie tak. Spróbuj ponownie, a jeżeli to nie pomoże to daj nam znać o błędzie.',
				type: ALERT_TYPES.ERROR,
			});
		},
		onUploadStarted() {
			this.loading = true;
		},
		onUploadSuccess(userData) {
			this.updateCurrentUserProfile(userData);
			this.loading = false;
		},
	},
};
</script>
