<template>
	<div>
		<div class="media-container">
			<div class="left">
				<wnl-avatar
					class="avatar"
					:full-name="author.full_name"
					:url="author.avatar"
					size="extralarge"
				/>
			</div>
			<div class="right">
				<div class="content">
					<div class="user-info-name-content">
						<div class="user-info-full-name">
							<span>{{author.full_name}}</span>
						</div>
					</div>
					<div v-if="author.city" class="user-info-city">
						<span class="icon is-small">
							<i class="fa fa-map-marker" />
						</span>
						<span class="city-title">{{author.city}}</span>
					</div>
					<div v-if="author.help" class="user-info-help">
						<span class="help-title">{{$t('user.userProfile.helpTitle')}}</span>
						<div class="notification">
							<span class="user-help">{{author.help}}</span>
						</div>
					</div>
				</div>
				<div class="level">
					<div class="level-left">
						<div class="send-message">
							<wnl-message-link :user-id="userId">
								<span class="button is-primary is-outlined is-small">{{$t('user.userProfileModal.sendMessage')}}</span>
							</wnl-message-link>
						</div>
					</div>
					<div class="level-right">
						<div class="redirect">
							<router-link :to="{ name: 'user', params: {userId: userId} }">
								<a class="button is-primary is-outlined is-small">{{$t('user.userProfileModal.redirectToProfile')}}</a>
							</router-link>
						</div>
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

	.user-info-full-name
		color: $color-ocean-blue
		font-size: $font-size-plus-5
		font-weight: $font-weight-bold
		margin-bottom: $margin-small
		line-height: $line-height-none

	.user-info-city
		color: $color-gray
		font-size: $font-size-base
		font-weight: $font-weight-light
		margin-bottom: $margin-small

	.user-info-help
		font-size: $font-size-base
	.notification
		width: 100%
		.help-title
			font-size: $font-size-minus-2
			text-transform: uppercase

	.send-message
		text-transform: uppercase

</style>

<script>
import MessageLink from 'js/components/global/MessageLink';

export default {
	name: 'UserProfileModal',
	components: {
		'wnl-message-link': MessageLink,
	},
	props: ['author'],
	data() {
		return {
			userId: this.author.user_id
		};
	},
};
</script>
