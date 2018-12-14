<template>
	<div class="wnl-previous-next">
		<div class="previous">
			<router-link class="nxt-prvs-link" :to="previousScreenRoute" v-if="previousScreenId">
				<span class="icon is-small">
					<i class="fa fa-arrow-circle-left"></i>
				</span>
				<span>{{previousScreenName}}</span>
			</router-link>
		</div>
		<div class="next">
			<router-link class="nxt-prvs-link" :to="nextScreenRoute" v-if="nextScreenId">
				<span>{{nextScreenName}}</span>
				<span class="icon is-small">
					<i class="fa fa-arrow-circle-right"></i>
				</span>
			</router-link>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.wnl-previous-next
		+white-shadow-top()

		border-top: $border-light-gray
		display: flex
		justify-content: space-between
		height: 100%

	.previous,
	.next
		align-items: stretch
		box-sizing: border-box
		display: flex
		flex: 1 auto
		max-width: 50%
		min-height: 100%

	.nxt-prvs-link
		align-items: center
		display: flex
		flex: 1 auto
		font-size: $font-size-minus-2
		font-weight: $font-weight-bold
		min-height: 100%
		padding: 0 20px
		text-transform: uppercase
		transition: all $transition-length-base
		width: 100%

		&:hover
			background: $color-background-light-gray
			transition: all $transition-length-base

		.icon
			color: $color-inactive-gray
			margin-right: $margin-small

	.next
		border-left: $border-light-gray

		.nxt-prvs-link
			justify-content: flex-end

			.icon
				margin-left: $margin-small
				margin-right: 0
</style>

<script>
import _ from 'lodash';
import { mapGetters } from 'vuex';
import { resource } from 'js/utils/config';

export default {
	name: 'PreviousNext',
	computed: {
		...mapGetters('course', [
			'getScreen',
			'getAdjacentScreenId',
		]),
		lessonId() {
			return this.$route.params.lessonId;
		},
		previousScreenId() {
			return this.getAdjacentScreenId(this.lessonId,
				this.$route.params.screenId, 'previous');
		},
		nextScreenId() {
			return this.getAdjacentScreenId(this.lessonId,
				this.$route.params.screenId, 'next');
		},
		previousScreenName() {
			return this.getScreen(this.previousScreenId).name;
		},
		nextScreenName() {
			return this.getScreen(this.nextScreenId).name;
		},
		previousScreenRoute() {
			return this.getAdjacentScreenRoute(this.previousScreenId);
		},
		nextScreenRoute() {
			return this.getAdjacentScreenRoute(this.nextScreenId);
		},
	},
	methods: {
		getAdjacentScreenRoute(id) {
			if (id === undefined) {
				return undefined;
			} else {
				return {
					name: resource('screens'),
					params: {
						courseId: this.$route.params.courseId,
						lessonId: this.lessonId,
						screenId: id,
					}
				};
			}
		},
	},
};
</script>
