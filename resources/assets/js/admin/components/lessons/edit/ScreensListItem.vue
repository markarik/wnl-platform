<template>
	<div class="media">
		<div class="media-left">
			<span class="icon is-small">
				<i class="fa fa-arrow-up"></i>
			</span>
			<span class="icon is-small">
				<i class="fa fa-arrow-down"></i>
			</span>
		</div>
		<div class="media-content">
			<router-link :to="to">{{name}}</router-link>
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

	.media-left,
	.media-content
		align-items: center
		display: flex
</style>

<script>
	export default {
		name: 'ScreensListItem',
		props: ['id', 'name'],
		computed: {
			to() {
				return {
					name: 'screen-edit',
					params: {
						lessonId: this.$route.params.lessonId,
						screenId: this.id,
					},
				}
			},
		},
	}
</script>
