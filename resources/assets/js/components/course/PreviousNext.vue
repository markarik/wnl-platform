<template>
	<div class="wnl-previous-next">
		<router-link class="nxt-prvs-link previous" :to="previousScreenRoute" v-if="previousScreenRoute !== undefined">
			Poprzedni
		</router-link>
		<router-link class="nxt-prvs-link next" :to="nextScreenRoute" v-if="nextScreenRoute !== undefined">
			Następny
		</router-link>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.wnl-previous-next
		+white-shadow-top()

		align-items: stretch
		border-top: $border-light-gray
		display: flex
		justify-content: space-between
		height: 100%

	.previous,
	.next
		align-items: center
		display: flex
		flex: 1 0 auto
		padding: 0 20px
		transition: all $transition-length-base

		&:hover
			background: $color-background-light-gray
			transition: all $transition-length-base

	.next
		justify-content: flex-end

	.previous
		border-right: $border-light-gray
</style>

<script>
	import _ from 'lodash'
	import { mapGetters } from 'vuex'
	import { resource } from 'js/utils/config'

	export default {
		name: 'PreviousNext',
		computed: {
			...mapGetters('course', ['getAdjacentScreenId']),
			previousScreenRoute() {
				return this.getAdjacentScreenRoute('previous')
			},
			nextScreenRoute() {
				return this.getAdjacentScreenRoute('next')
			},
		},
		methods: {
			getAdjacentScreenRoute(direction) {
				let lessonId = this.$route.params.lessonId,
					id = this.getAdjacentScreenId(
						lessonId,
						this.$route.params.screenId,
						direction)

				if (id === undefined) {
					return undefined
				} else {
					return {
						name: resource('screens'),
						params: {
							courseId: this.$route.params.courseId,
							lessonId: lessonId,
							screenId: id,
						}
					}
				}
			},
		},
	}
</script>
