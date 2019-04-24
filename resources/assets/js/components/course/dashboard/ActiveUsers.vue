<template>
	<div v-if="activeUsersCount" class="active-users">
		<div v-t="{ path: message, args: { count: activeUsersCount } }" class="metadata" />
		<div class="active-users-container">
			<div class="absolute-container">
				<ul ref="avatarsList" class="avatars-list">
					<li
						v-for="(user, index) in usersToCount"
						:key="index"
						class="avatar"
					>
						<div class="activator" @click="toggleModal(true, user.profile)">
							<wnl-avatar
								:full-name="user.profile.full_name"
								:url="user.profile.avatar"
								:roles="user.profile.roles"
								size="medium"
							/>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<wnl-modal v-if="modalVisible" @closeModal="toggleModal(false)">
			<wnl-user-profile-modal :profile="modalUser" />
		</wnl-modal>
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
		cursor: pointer

</style>

<script>
import { mapGetters } from 'vuex';

import UserProfileModal from 'js/components/users/UserProfileModal';
import Modal from 'js/components/global/Modal';

export default {
	name: 'ActiveUsers',
	components: {
		'wnl-user-profile-modal': UserProfileModal,
		'wnl-modal': Modal
	},
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
	data() {
		return {
			modalVisible: false,
			modalUser: {}
		};
	},
	computed: {
		...mapGetters(['currentUserId']),
		...mapGetters('users', ['activeUsers']),
		activeUsersCount() {
			return this.usersToCount.length;
		},
		usersToCount() {
			return this.activeUsers(this.channel).filter((user) => this.currentUserId !== user.id);
		},
	},
	methods: {
		toggleModal(isVisible, modalUser={}) {
			this.modalVisible = isVisible;
			this.modalUser = modalUser;
		}
	}
};
</script>
