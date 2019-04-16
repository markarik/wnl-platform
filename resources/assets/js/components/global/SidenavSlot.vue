<template>
	<div
		v-if="isVisible"
		ref="slot"
		class="wnl-sidenav-slot"
		:class="{ 'wnl-sidenav-detached': isDetached, 'has-chat': hasChat, 'is-max-width': isMaxWidth, 'is-narrow': isNarrow }"
		@click="onClick"
	>
		<div class="sidenav-content">
			<slot></slot>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-sidenav-slot
		border-right: $border-light-gray
		display: flex
		width: $sidenav-width
		&.is-max-width
			min-width: $sidenav-for-messages

		&.has-chat
			border-left: $border-light-gray
			flex-direction: column
			width: $course-chat-width

			.sidenav-content
				flex: 1

		.sidenav-content
			display: flex
			height: $main-height
			width: 100%

		&.is-narrow
			width: initial
			border-right: none

	.wnl-sidenav-detached
		background: rgba(0, 0, 0, 0.8)
		border: none
		bottom: 0
		left: 0
		position: fixed
		right: 0
		top: $navbar-height
		width: initial
		z-index: $z-index-sidenav-slot

		&.has-chat
			flex-direction: row
			justify-content: flex-end
			width: initial

			.sidenav-content
				max-width: 600px

		.sidenav-content
			background: $color-white
			flex-direction: column
			height: 100%
			max-width: 400px
			width: 100%

		.sidenav-aside
			max-width: none
			width: auto

		.wnl-sidenav,
		.sidenav-aside
			padding: 0
</style>

<script>
import { mapActions } from 'vuex';

export default {
	props: {
		isVisible: {
			type: Boolean,
			default: false,
		},
		isDetached: {
			type: Boolean,
			default: false,
		},
		hasChat: {
			type: Boolean,
			default: false,
		},
		isMaxWidth: {
			type: Boolean,
			default: false,
		},
		isNarrow: {
			type: Boolean,
			default: false,
		},
	},
	methods: {
		...mapActions(['closeSidenavs']),
		onClick(event) {
			event.target === this.$refs.slot && this.closeSidenavs();
		}
	}
};
</script>
