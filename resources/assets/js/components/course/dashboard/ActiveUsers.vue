 <template>
	<div>
		<div class="metadata">
			{{ $t('dashboard.activeUsers', {count: activeUsersCount}) }}
		</div>
		<div class="active-users-container" v-if="activeUsersCount">
			<div class="absolute-container">
				<ul class="avatars-list" ref="avatarsList">
					<li v-for="user in usersToCount" class="avatar">
						<wnl-avatar
								:fullName="user.fullName"
								:url="user.avatar"
								:userId="user.id"
								:user="user"
								size="medium">
						</wnl-avatar>
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
	import {mapGetters} from 'vuex'

	export default {
		name: 'ActiveUsers',
		computed: {
			...mapGetters(['activeUsers', 'currentUserId', 'currentUserName']),
			usersToCount() {
				return this.activeUsers.filter((user) => this.currentUserId !== user.id)
			},
			activeUsersCount() {
				return this.usersToCount.length || 0
			},
		},
	}
</script>
