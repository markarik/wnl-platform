<template>
	<div class="media">
		<div class="media-left">
			<span class="icon is-small" @click="moveScreen('up')" v-if="!isFirst">
				<i class="fa fa-arrow-up"></i>
			</span>
			<span class="icon is-small" @click="moveScreen('down')" v-if="!isLast">
				<i class="fa fa-arrow-down"></i>
			</span>
		</div>
		<div class="media-content">
			<router-link :to="to" v-if="isLink">{{screen.name}}</router-link>
			<span v-else>{{screen.name}}</span>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.icon
		color: $color-gray-dimmed
		cursor: pointer

	.media
		align-items: stretch

	.media-left
		flex-direction: column

		.icon
			padding: $margin-base 0

			&:hover
				color: $color-ocean-blue

	.media-left,
	.media-content
		align-items: center
		display: flex
</style>

<script>
	export default {
		name: 'ScreensListItem',
		props: ['index', 'screen', 'isFirst', 'isLast'],
		computed: {
			to() {
				return {
					name: 'screen-edit',
					params: {
						lessonId: this.$route.params.lessonId,
						screenId: this.screen.id,
					},
				}
			},
			isLink() {
				return this.screen.type !== 'slideshow'
			}
		},
		methods: {
			moveScreen(direction) {
				this.$emit('moveScreen', {
					from: this.index,
					to: direction === 'up' ? this.index - 1 : this.index + 1,
				})
			}
		},
	}
</script>
