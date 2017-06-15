<template>
	<div class="vote" :class="[iconClass, {'flash': wasJustClicked}]" @click="toggleReaction">
		<span class="icon is-small">
			<i class="fa" :class="hasReactedClass"></i>
		</span>
		<span class="count">{{ countCopy }}</span>
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
		position: relative
		transition: all 0.2s
		width: 3em

	.flash-bg
		border-radius: 100px
		left: 50%
		position: absolute
		opacity: 0.5
		top: 50%

		&.flash
			animation: flash 1s ease-out

	.vote-up
		color: $color-green
		flex-direction: column

	.vote-down
		color: $color-red
		flex-direction: column-reverse

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
	import { nextTick } from 'vue'
	import { mapGetters, mapActions } from 'vuex'

	import { scrollToElement } from 'js/utils/animations'

	export default {
		name: 'Vote',
		props: ['type', 'module', 'reactableResource', 'reactableId'],
		data() {
			return {
				countCopy: 0,
				hasReactedCopy: false,
				isLoading: false,
				wasJustClicked: false,
			}
		},
		computed: {
			...mapGetters('qna', ['getReaction']),
			reaction() {
				return this.getReaction(this.reactableResource, this.reactableId, 'upvote')
			},
			iconClass() {
				return `vote-${this.type}`
			},
			count() {
				return this.reaction.count
			},
			hasReactedClass() {
				return this.hasReactedCopy ? 'fa-thumbs-up' : 'fa-thumbs-o-up'
			},
		},
		methods: {
			...mapActions('qna', ['setReaction']),
			toggleReaction() {
				if (this.isLoading) {
					return false
				}
				this.isLoading = true
				this.wasJustClicked = true
				this.setReaction({
					reactableResource: this.reactableResource,
					reactableId: this.reactableId,
					reaction: 'upvote',
					hasReacted: this.hasReactedCopy,
					preventUpdate: true,
				}).then((response) => {
					if (this.hasReactedCopy) {
						this.countCopy--
					} else {
						this.countCopy++
					}
					this.hasReactedCopy = !this.hasReactedCopy
					this.isLoading = false
					nextTick(() => {
						this.wasJustClicked = false
					})
				})
				.catch((error) => {
					$wnl.logger.error(error)
					this.isLoading = false
					nextTick(() => {
						this.wasJustClicked = false
					})
				})
			},
		},
		mounted() {
			this.countCopy = this.count
			this.hasReactedCopy = this.reaction.hasReacted
		}
	}
</script>
