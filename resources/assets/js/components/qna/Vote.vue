<template>
	<div class="vote" :class="iconClass">
		<span class="icon is-small">
			<i class="fa" :class="hasReactedClass" @click="toggleReaction"></i>
		</span>
		<span class="count">{{ count }}</span>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.vote
		align-items: center
		cursor: pointer
		font-size: $font-size-minus-2
		font-weight: $font-weight-bold
		display: flex

	.vote-up
		color: $color-green
		flex-direction: column

	.vote-down
		color: $color-red
		flex-direction: column-reverse
</style>

<script>
	import { mapGetters, mapActions } from 'vuex'

	export default {
		name: 'Vote',
		props: ['type', 'module', 'reactableResource', 'reactableId'],
		data() {
			return {
				isLoading: false
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
				return this.reaction.hasReacted ? 'fa-thumbs-up' : 'fa-thumbs-o-up'
			},
		},
		methods: {
			...mapActions('qna', ['setReaction']),
			toggleReaction() {
				if (this.isLoading) {
					return false
				}
				this.isLoading = true
				this.setReaction({
					reactableResource: this.reactableResource,
					reactableId: this.reactableId,
					reaction: 'upvote',
					hasReacted: this.reaction.hasReacted,
				}).then((response) => {
					this.isLoading = false
					console.log('response z akcji komponentu :');
					console.log(response);
				})
				.catch((error) => {
					$wnl.logger.error(error)
					this.isLoading = false
				})
			},
		},
	}
</script>
