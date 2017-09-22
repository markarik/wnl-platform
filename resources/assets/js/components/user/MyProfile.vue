<template lang="html">
	<div class="scrollable-main-container wnl-user-profile" :class="{mobile: isMobileProfile}">
		<div class="wnl-user-stats" v-if="currentUserStats"
			<div class="level wnl-screen-title">
				<div class="level-left">
					<div class="level-item big strong">
						Statystyki
					</div>
				</div>
			</div>

			<div class="level-left">Liczba spędzonych minut na platformie: {{time}}</div>
			<div class="level-left">Procent przerobionych lekcji: {{lessonsCompleted}}</div>
			<div class="level-left">Procent rozpoczętych lekcji: {{lessonsStarted}}</div>
			<div class="margin vertical">
		</div>

		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Profil publiczny
				</div>
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
				<wnl-avatar size="large" class="clickable-avatar"></wnl-avatar>
				<a class="button is-small is-outlined is-primary margin top" :class="{'is-loading': loading}">
					Zmień avatar
				</a>
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
				align-items: center
				display: flex
				flex-direction: column
				margin-top: 12px

			.button
				margin-top: 20px

			form
				padding: 0 5%

		.clickable-avatar
			cursor: pointer
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
			}
		},
		computed: {
			...mapGetters(['isMobileProfile', 'currentUserStats']),
			isProduction() {
				return isProduction()
			},
			time() {
				return moment.duration({...this.currentUserStats.time}).asMinutes()
			},
			lessonsStarted() {
				const {started, total} = this.currentUserStats.progress;
				return Math.floor(started / total * 100)
			},
			lessonsCompleted() {
				const {total, completed} = this.currentUserStats.progress;
				return Math.floor(completed / total * 100)
			}
		},
		methods: {
			...mapActions(['updateCurrentUser', 'fetchCurrentUserStats']),
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
		mounted() {
			this.fetchCurrentUserStats();
		}
	}
</script>
