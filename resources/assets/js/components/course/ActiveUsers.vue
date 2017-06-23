<template>
	<div>
		<p>Uczą się razem z Tobą ({{activeUsersCount}}):</p>
		<ul class="avatars-list">
			<li v-for="user in usersToDisplay" class="avatar">
				<wnl-avatar
						:fullName="user.fullName"
						:url="user.avatar"
						size="medium">
				</wnl-avatar>
			</li>
		</ul>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	.avatars-list
		display: flex
	.avatars-list .avatar
		margin-right: 5px
</style>

<script>
	import {mapGetters} from 'vuex'

	export default {
		name: 'ActiveUsers',
		computed: {
			...mapGetters(['activeUsers', 'currentUserId']),
			activeUsersCount() {
				return this.usersToDisplay.length || 0
			},
			usersToDisplay() {
				return this.activeUsers.filter((user) => this.currentUserId !== user.id).slice(0, 10)
			}
		},
	}
</script>
