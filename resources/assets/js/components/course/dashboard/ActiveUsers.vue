<template>
	<div class="active-users-container" v-if="activeUsersCount">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="active-users-title level-item metadata">Uczą się teraz z Tobą ({{activeUsersCount}})</div>
			</div>
		</div>
		<div class="absolute-container">
			<ul class="avatars-list" ref="avatarsList">
				<li v-for="user in usersToCount" class="avatar">
					<wnl-avatar
							:fullName="user.fullName"
							:url="user.avatar"
							size="medium">
					</wnl-avatar>
				</li>
			</ul>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.wnl-screen-title
		margin-bottom: $margin-small

	.active-users-container
		padding-bottom: $margin-big
		position: relative

	.absolute-container
		position: absolute
		bottom: 0
		left: 0
		right: 0
		top: $margin-big

	.avatars-list
		display: flex
		overflow: hidden
		position: relative

		&::after
			content: ""
			height: map-get($rounded-square-sizes, 'medium')
			position: absolute
			right: 0
			width: map-get($rounded-square-sizes, 'medium') * 2
			+gradient-horizontal(rgba(0,0,0,0), $color-white)

	.avatars-list .avatar
		margin-right: $margin-small
</style>

<script>
	import {mapGetters} from 'vuex'

	export default {
		name: 'ActiveUsers',
		computed: {
			...mapGetters(['activeUsers', 'currentUserId']),
			usersToCount() {
				return this.activeUsers.filter((user) => this.currentUserId !== user.id)
			},
			activeUsersCount() {
				return this.usersToCount.length || 0
			},
		},
	}
</script>
