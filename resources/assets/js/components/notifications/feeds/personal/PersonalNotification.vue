<template>
	<div class="notification-wrapper">
		<div class="avatar" @click.stop="showModal">
			<wnl-avatar
				size="medium"
				:full-name="message.actors.full_name"
				:url="message.actors.avatar"
				:roles="message.actors.roles"
			/>
		</div>
		<div
			class="personal-notification"
			:class="{'deleted': deleted || resolved}"
			@click="markAsReadAndGo"
		>
			<div class="notification-content">
				<div class="notification-header">
					<span class="actor">{{message.actors.full_name}}</span>
					<span class="action">{{action}}</span>
					<span v-if="object" class="object">{{object}}</span>
					<span v-if="contextInfo" class="context">{{contextInfo}}</span>
					<span v-if="objectText" class="object-text wrap">{{objectText}}</span>
				</div>
				<div v-if="subjectText" class="subject wrap">{{subjectText}}</div>
				<div class="time">
					<span class="icon is-small">
						<i class="fa" :class="icon" />
					</span>{{formattedTime}}
				</div>
			</div>
			<div class="link-symbol">
				<span
					v-if="hasContext"
					class="icon"
					:class="{'unread': !isRead}"
				>
					<i v-if="loading" class="loader" />
					<i v-else class="fa fa-angle-right" />
				</span>
			</div>
		</div>
		<div
			v-if="deleted"
			v-t="'notifications.messages.deleted'"
			class="delete-message"
		/>
		<div
			v-if="resolved"
			v-t="'notifications.messages.resolved'"
			class="delete-message"
		/>
		<wnl-modal v-if="isVisible" @closeModal="closeModal">
			<wnl-user-profile-modal :profile="userForModal" />
		</wnl-modal>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.notification-wrapper
		border-bottom: $border-light-gray
		display: flex
		justify-content: space-between

		.avatar
			font-weight: $font-weight-bold
			padding: $margin-small
			transition: background-color $transition-length-base

			&:hover
				background: $color-background-lighter-gray
				cursor: pointer
				transition: background-color $transition-length-base

		.personal-notification
			align-items: flex-start
			display: flex
			font-size: $font-size-minus-1
			justify-content: space-between
			padding: $margin-small
			position: relative
			transition: background-color $transition-length-base

			&:hover
				background: $color-background-lighter-gray
				cursor: pointer
				transition: background-color $transition-length-base

			.notification-content
				flex: 1 auto

				.notification-header
					line-height: $line-height-minus

				.context
					font-weight: $font-weight-bold

				.object-text,
				.subject

					&::before
						content: '« '

					&::after
						content: ' »'

				.object-text
					color: $color-gray

				.subject
					font-size: $font-size-base
					line-height: $line-height-minus
					margin-top: $margin-small

				.time
					color: $color-background-gray
					font-size: $font-size-minus-2
					margin-top: $margin-small

					.icon
						margin-right: $margin-small

			.link-symbol
				display: flex
				flex: 0

				.icon
					color: $color-inactive-gray

					&.unread
						color: $color-ocean-blue
</style>

<script>
import { camelCase } from 'lodash';
import { mapGetters } from 'vuex';

import Avatar from 'js/components/global/Avatar';
import UserProfileModal from 'js/components/users/UserProfileModal';
import Modal from 'js/components/global/Modal';
import { notification } from 'js/components/notifications/notification';

export default {
	name: 'PersonalNotification',
	components: {
		'wnl-avatar': Avatar,
		'wnl-modal': Modal,
		'wnl-user-profile-modal': UserProfileModal
	},
	mixins: [notification],
	props: {
		icon: {
			required: true,
			type: String
		},
	},
	data() {
		return {
			isVisible: false
		};
	},
	computed: {
		...mapGetters(['currentUserId']),
		userForModal() {
			return {
				...this.message.actors,
				user_id: this.message.actors.id
			};
		},
		action() {
			return this.$t(`notifications.events.${camelCase(this.message.event)}`);
		},
		object() {
			const objects = this.message.objects;
			if (!objects) return false;

			return this.$tc(
				`notifications.objects.${camelCase(objects.type)}`,
				this.currentUserId === objects.author ? 2 : 1
			);
		},
	},
	methods: {
		showModal() {
			this.isVisible = true;
		},
		closeModal() {
			this.isVisible = false;
		},
		dispatchGoToContext() {
			this.goToContext();
			this.loading = false;
		},
		markAsReadAndGo() {
			if(!this.hasContext) return false;

			this.loading = true;

			if (!this.isRead) {
				this.markAsRead({ notification: this.message, channel: this.channel })
					.then(() => {
						this.dispatchGoToContext();
					});
			} else {
				this.dispatchGoToContext();
			}
		},
	},
};
</script>
