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
            <wnl-text></wnl-text>
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
</style>

<script>
	import { mapActions, mapGetters } from 'vuex'

    import Avatar from 'js/components/global/Avatar'
	import Upload from 'js/components/global/Upload'
	import Text from 'js/components/global/form/Text'
	import { isProduction } from 'js/utils/env'

	export default {
		name: 'UserProfile',
		components: {
            'wnl-avatar': Avatar,
			'wnl-text': Text,
			'wnl-upload': Upload,
		},
		props: ['response'],
		data() {
			return {
				loading: false,
			}
		},
		computed: {
			...mapGetters(['isMobileProfile']),
			isProduction() {
				return isProduction()
			}
		},
	}
</script>
