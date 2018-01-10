<template>
	<div class="active-users" v-if="activeUsersCount">
		<div class="metadata" v-t="{ path: message, args: { count: activeUsersCount } }">
		</div>
		<div class="active-users-container">
			<div class="absolute-container">
				<ul class="avatars-list" ref="avatarsList">
					<li v-for="(user, index) in usersToCount" class="avatar" :key="index">
						<div class="activator" @click="showModal(user)">
							<wnl-avatar
								:fullName="user.fullName"
								:url="user.avatar"
								size="medium">
							</wnl-avatar>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	$square-size: 'medium'
	$container-height: map-get($rounded-square-sizes, $square-size)

	.metadata
		border-bottom: $border-light-gray
		margin-bottom: $margin-small

	.wnl-screen-title
		margin-bottom: $margin-small

	.active-users-container
		height: $container-height
		padding-bottom: $margin-big
		position: relative

	.absolute-container
		position: absolute
		bottom: 0
		left: 0
		right: 0
		top: 0

	.avatars-list
		display: flex
		overflow: hidden
		position: relative

		&::after
			content: ""
			height: $container-height
			position: absolute
			right: 0
			width: $container-height * 2
			+gradient-horizontal(rgba(0,0,0,0), $color-white)

	.avatars-list .avatar
		margin-right: $margin-small
</style>

<script>
import { mapGetters, mapActions } from 'vuex'
	export default {
		name: 'ActiveUsers',
		props: {
			channel: {
				type: String,
				default: 'activeUsers'
			},
			message: {
				type: String,
				default: 'dashboard.activeUsers',
			},
		},
		computed: {
			...mapGetters(['currentUserId']),
			...mapGetters('users', ['activeUsers']),
			activeUsersCount() {
				return this.usersToCount.length
			},
			usersToCount() {
				return this.activeUsers(this.channel).filter((user) => this.currentUserId !== user.id)
			},
		},
		methods: {
			...mapActions(['toggleModal']),
			showModal(user) {
				this.toggleModal({
					visible: true,
					content: {
						author: user.profile
					},
					component: 'wnl-user-profile-modal',
				})
			}
		}
	}
</script>
