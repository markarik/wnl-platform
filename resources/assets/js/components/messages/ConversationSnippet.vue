<template>
	<div :class="{'active-in-route': isActive, 'conversation-snippet': true}">
		<figure class="media-left">
			<wnl-avatar
				:full-name="profile.full_name"
				:url="profile.avatar"
				size="medium"
			>
			</wnl-avatar>
		</figure>
		<div class="media-content">
			<div class="content">
				<div class="conversation-meta">
					<div class="conversation-names">
						<span class="full-name">{{profile.full_name}}</span>
					</div>

					<div v-if="room && room.last_message_time" class="conversation-time">
						<small>{{time(room.last_message_time)}}</small>
					</div>
				</div>
				<div v-if="bothNames">
					<span class="full-name">{{profile.full_name}}</span>
				</div>

				<div class="conversation-message" v-html="lastMessageContent" />
			</div>
		</div>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.conversation-snippet
		display: flex
		min-width: 0
		overflow: hidden
		padding: $margin-medium
		cursor: pointer
		min-height: $min-height

		&:hover, &.active-in-route
			background-color: $color-background-lightest-gray

		.media-left
			display: flex
			align-items: center

		.media-content
			min-width: 0
			overflow: hidden
			display: flex
			align-items: center

			.content
				flex: 1
				max-width: 100%

			*
				font-weight: $font-weight-regular

			.conversation-meta
				line-height: $line-height-minus
				display: flex
				overflow: hidden
				justify-content: space-between

				.conversation-names
					min-width: 0
					flex: 1 1 0%
					overflow: hidden
					white-space: nowrap
					text-overflow: ellipsis

					.full-name
						font-size: $font-size-base

				.conversation-time
					display: flex
					min-width: 0

			.full-name
				font-size: $font-size-minus-1

			.conversation-message p
				line-height: $line-height-minus
				min-width: 0
				flex: 1 1 0%
				overflow: hidden
				white-space: nowrap
				text-overflow: ellipsis
				max-height: 1.5em

	.has-unread .conversation-message p
		font-weight: $font-weight-bold

</style>

<script>
import { shortTimeFromMs } from 'js/utils/time';
import { last } from 'lodash';

export default {
	name: 'ConversationSnippet',
	props: {
		room: {
			required: false,
			default: () => ({})
		},
		profile: {
			required: false,
			type: Object,
			default: () => ({})
		},
		bothNames: {
			type: Boolean,
			default: false
		},
		isActive: {
			type: Boolean,
			default: false
		}
	},
	computed: {
		messages() {
			return this.room.messages || [];
		},
		lastMessageContent() {
			if (!this.messages || !this.messages.length) {
				return '';
			}

			return last(this.messages).content;
		},
		roomId() {
			if (!this.room) {
				return 0;
			}

			return this.room.id;
		}
	},
	methods: {
		time(stamp){
			return shortTimeFromMs(stamp);
		}
	}
};
</script>
