<template>
	<div class="wnl-user-profile-modal">
		<div class="activator">
			<a class="qna-author-name" @click="activateModal">
				{{authorNameToDisplay}} ·
			</a>
		</div>
		<div class="modal" :class="{'is-active': showModal}">
			<div class="modal-background" @click="deactivateModal"></div>
			<div class="modal-content">
				<div class="box">
					<div class="media">
						<div class="media-left">
							<wnl-avatar class="avatar"
								:fullName="author.full_name"
								:url="author.avatar"
								:userId="userId"
								size="extralarge"/>
						</div>
						<div class="media-content">
							<div class="content">
								<div class="user-info-name">
									<span>{{this.author.display_name}}</span>
								</div>
								<div  v-if="cityToDisplay" class="user-info-city">
									<span class="icon is-small">
										<i class="fa fa-map-marker"></i>
									</span>
									<span class="city-title">{{ this.author.city }}</span>
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
									<router-link :to="{ name: 'user', params: {userId: userId} }">
										<a class="button is-primary is-outlined is-small">Zobacz pełen profil</a>
									</router-link>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<button class="modal-close is-large" aria-label="close" @click="deactivateModal"></button>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.modal
		z-index: $z-index-alerts

	.user-info-name
		color: $color-ocean-blue-opacity
		font-size: $font-size-plus-2
		font-weight: $font-weight-bold
		margin-bottom: $margin-tiny

	.user-info-city
		color: $color-gray-dimmed
		font-size: $font-size-base
		font-weight: $font-weight-light

	.user-info-help
		.help-title
			font-size: $font-size-minus-2
			text-transform: uppercase

</style>

<script>
import Avatar from 'js/components/global/Avatar'

export default {
	name: 'UserProfileModal',
	props: ['author', 'userId'],
	data() {
		return {
			showModal: false,
		}
	},
	computed: {
		authorNameToDisplay() {
			return this.author.display_name || this.author.full_name
		},
		cityToDisplay() {
			return this.author.city
		},
		helpToDisplay() {
			return this.author.help
		}
	},
	methods: {
		activateModal() {
			this.showModal = true
		},
		deactivateModal() {
			this.showModal = false
		},
	},
}
</script>
