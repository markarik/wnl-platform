<template>
	<div>
		<div class="media-container">
			<div class="left">
				<wnl-avatar class="avatar"
					:fullName="author.full_name"
					:url="author.avatar"
					:userId="userId"
					size="extralarge"/>
			</div>
			<div class="right">
				<div class="content">
					<div class="user-info-name">
						<span>{{ author.display_name }}</span>
					</div>
					<div  v-if="cityToDisplay" class="user-info-city">
						<span class="icon is-small">
							<i class="fa fa-map-marker"></i>
						</span>
						<span class="city-title">{{ author.city }}</span>
					</div>
					<div v-if="helpToDisplay" class="user-info-help">
						<span class="help-title">{{ $t('user.userProfile.helpTitle') }}</span>
						<div class="notification">
							<span class="user-help">{{ helpToDisplay }}</span>
						</div>
					</div>
				</div>
				<div class="navigation">
					<div class="redirect">
						<router-link v-on:click.native="deactivateModal" :to="{ name: 'user', params: {userId: userId} }">
							<a class="button is-primary is-outlined is-small">Zobacz pełen profil</a>
						</router-link>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.media-container
		display: flex
		flex-direction: row
		justify-content: flex-start
		margin-bottom: $margin-base
		.left
			margin-right: $margin-base
		.right
			width: 100%

	.user-info-name
		margin-top: $margin-tiny
		color: $color-ocean-blue-opacity
		font-size: $font-size-plus-2
		font-weight: $font-weight-bold
		margin-bottom: $margin-tiny

	.user-info-city
		color: $color-gray-dimmed
		font-size: $font-size-base
		font-weight: $font-weight-light

	.user-info-help
		font-size: $font-size-base
	.notification
		width: 100%
		.help-title
			font-size: $font-size-minus-2
			text-transform: uppercase

</style>

<script>
import { mapActions } from 'vuex'

import Avatar from 'js/components/global/Avatar'

export default {
	name: 'UserProfileModal',
	props: ['author'],
	data() {
		return {
			showModal: false,
			userId: this.author.user_id,
		}
	},
	computed: {
		cityToDisplay() {
			return this.author.city
		},
		helpToDisplay() {
			return this.author.help
		},
	},
	methods: {
		...mapActions(['toggleModal']),
		deactivateModal() {
			this.toggleModal({
				visible: false,
			})
		}
	}
}
</script>
