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
                class="image is-128x128" size="huge"></wnl-avatar>
				<hr>
		</div>

		<div class="margin vertical">
            <p>{{ this.response.data.first_name }}</p>
        </div>


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
</style>

<script>
	import { mapActions, mapGetters } from 'vuex'

    import Avatar from 'js/components/global/Avatar'
	import Upload from 'js/components/global/Upload'
	import { Form, Text } from 'js/components/global/form'
	import { isProduction, getApiUrl } from 'js/utils/env'

	export default {
		name: 'UserProfile',
		components: {
            'wnl-avatar': Avatar,
			'wnl-form': Form,
			'wnl-form-text': Text,
			'wnl-upload': Upload,
		},
		data() {
			return {
				loading: false,
                param: this.$route.params.userId,
                response: {}
			}
		},
		computed: {
			...mapGetters(['isMobileProfile']),
			isProduction() {
				return isProduction()
			}
		},
        mounted() {
            axios.get(getApiUrl(`users/${this.param}/profile`))
				.then((response) => {
					this.response = response
				})
			.catch(exception => $wnl.logger.capture(exception))
        },
	}
</script>
