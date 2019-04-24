<template>
	<article
		class="media wnl-chat-message"
		:class="{ 'is-full': showAuthor }"
		:data-id="id"
	>
		<figure
			class="media-left"
			:class="{'author-forgotten': author.deleted_at}"
			@click="showModal"
		>
			<wnl-avatar
				v-if="showAuthor"
				:full-name="fullName"
				:url="avatar"
			/>
			<div v-else class="media-left-placeholder" />
		</figure>
		<div class="media-content">
			<div class="content">
				<p v-if="showAuthor" class="wnl-message-meta">
					<strong
						class="author"
						:class="{'author-forgotten': author.deleted_at}"
						@click="showModal"
					>{{fullName}}</strong>
					<small class="wnl-message-time">{{formattedTime}}</small>
				</p>
				<wnl-user-signature :user="author"></wnl-user-signature>
				<p class="wnl-message-content" v-html="content" />
			</div>
		</div>
		<wnl-modal v-if="isVisible" @closeModal="closeModal">
			<wnl-user-profile-modal :author="author" />
		</wnl-modal>
	</article>
</template>
<style lang="sass">
	@import 'resources/assets/sass/variables'

	.media.wnl-chat-message
		border-top: 0
		line-height: 24px
		padding-top: 0
		margin-top: 0.5rem

		&.is-full
			margin-top: 1.25rem

		.media-left
			margin: 0 $margin-small 0 0
			cursor: pointer
			&.author-forgotten
				color: $color-gray
				pointer-events: none

		.media-left-placeholder
			height: 1px
			width: map-get($rounded-square-sizes, 'medium')

		.media-content
			.content
				color: $color-darker-gray
				word-wrap: break-word
				word-break: break-word

				.wnl-message-meta
					color: $color-inactive-gray
					line-height: 1em
					margin-bottom: $margin-tiny
					.author
						cursor: pointer
						color: $color-sky-blue
						&.author-forgotten
							color: $color-gray
							pointer-events: none

				.wnl-message-time
					margin-left: $margin-small

				p
					margin: 0
</style>
<script>
import { timeFromMs } from 'js/utils/time';

import Modal from 'js/components/global/Modal.vue';
import UserProfileModal from 'js/components/users/UserProfileModal';
import Avatar from 'js/components/global/Avatar';
import UserSignature from 'js/components/global/UserSignature';

export default{
	components: {
		'wnl-avatar': Avatar,
		'wnl-user-profile-modal': UserProfileModal,
		'wnl-modal': Modal,
		'wnl-user-signature': UserSignature,
	},
	props: ['author', 'avatar', 'time', 'showAuthor', 'content', 'id', 'fullName'],
	data() {
		return {
			isVisible: false
		};
	},
	computed: {
		formattedTime () {
			return timeFromMs(this.time);
		},
	},
	methods: {
		showModal() {
			this.isVisible = true;
		},
		closeModal() {
			this.isVisible = false;
		}
	}
};
</script>
