<template>
	<div class="vote" :class="iconClass" @click="toggleReaction">
		<span class="icon is-small">
			<i class="fa" :class="hasReactedClass"></i>
		</span>
		<span class="count">{{ count }}</span>
		<span class="flash-bg" :class="{'flash': wasJustClicked}"></span>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.vote
		align-items: center
		cursor: pointer
		display: flex
		font-size: $font-size-minus-2
		font-weight: $font-weight-bold
		padding: 0 $margin-small
		position: relative
		transition: all 0.2s

	.vote-up
		color: $color-green
		flex-direction: column

	.vote-down
		color: $color-red
		flex-direction: column-reverse

	.flash-bg
		border-radius: 100px
		left: 50%
		position: absolute
		opacity: 0.5
		top: 50%

		&.flash
			animation: flash 1s ease-out

	@keyframes flash
		0%
			background: transparent
			height: 0
			width: 0
		20%
			background: $color-green
		100%
			background: transparent
			height: 50px
			left: -10px
			top: -10px
			width: 50px
</style>

<script>
import {
	reaction
} from 'js/mixins/reaction';

export default {
	name: 'Vote',
	mixins: [reaction],
	props: ['type'],
	data() {
		return {
			isLoading: false,
			name: 'upvote',
		};
	},
	computed: {
		iconClass() {
			return `vote-${this.type}`;
		},
		hasReactedClass() {
			return this.hasReacted ? 'fa-thumbs-up' : 'fa-thumbs-o-up';
		},
	},
};
</script>
