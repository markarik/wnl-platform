<template>
	<div>
		<h4 class="title is-5">Uczą się razem z Tobą ({{activeUsersCount}})</h4>
		<ul v-if="activeUsersCount" class="avatars-list" ref="avatarsList">
			<li v-for="user in usersToCount" class="avatar">
				<wnl-avatar
						:fullName="user.fullName"
						:url="user.avatar"
						size="medium">
				</wnl-avatar>
			</li>
		</ul>
		<p v-else>nikogo nie ma, gdzie są wszyscy? :(</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.title
		margin-bottom: $margin-small
	.avatars-list
		display: flex
		overflow: hidden
		&::after
			content: ""
			height: map-get($rounded-square-sizes, 'medium')
			position: absolute
			right: $column-padding;
			width: map-get($rounded-square-sizes, 'medium') * 2
			+gradient-horizontal(rgba(0,0,0,0), $color-white)
	.avatars-list .avatar
		margin-right: 5px
</style>

<script>
	import {mapGetters} from 'vuex'

	export default {
		name: 'ActiveUsers',
		computed: {
			...mapGetters(['activeUsers', 'currentUserId']),
			usersToCount() {
				return this.activeUsers.filter((user) => this.currentUserId !== user.id);
			},
			activeUsersCount() {
				return this.usersToCount.length || 0
			}
		},
	}
</script>
