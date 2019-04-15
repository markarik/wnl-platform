<template>
	<div class="media">
		<div class="media-left">
			<span
				class="icon is-small"
				@click="moveScreen('up')"
				v-if="!isFirst"
			>
				<i class="fa fa-arrow-up"></i>
			</span>
			<span class="icon is-small is-disabled" v-else>
				<i class="fa fa-arrow-up"></i>
			</span>

			<span
				class="icon is-small"
				@click="moveScreen('down')"
				v-if="!isLast"
			>
				<i class="fa fa-arrow-down"></i>
			</span>
			<span class="icon is-small is-disabled" v-else>
				<i class="fa fa-arrow-down"></i>
			</span>
		</div>
		<div class="media-content">
			<router-link :to="to" v-if="isLink">{{screen.name}}</router-link>
			<span v-else>{{screen.name}}</span>
		</div>
		<div class="media-right">
			<span class="icon is-small" @click="deleteScreen()">
				<i class="fa fa-trash"></i>
			</span>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.icon
		color: $color-gray
		cursor: pointer

	.media
		align-items: stretch

	.media-left
		flex-direction: column

		.icon
			padding: $margin-base 0

			&:hover
				color: $color-ocean-blue

			&.is-disabled,
			&.is-disabled:hover
				color: $color-inactive-gray
				cursor: not-allowed

	.media-right
		align-items: center
		display: flex

		.icon:hover
			color: $color-red

	.media-left,
	.media-content
		align-items: center
		display: flex
</style>

<script>
import { swalConfig } from 'js/utils/swal';

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
			};
		},
		isLink() {
			return this.screen.type !== 'slideshow';
		}
	},
	methods: {
		moveScreen(direction) {
			this.$emit('moveScreen', {
				from: this.index,
				to: direction === 'up' ? this.index - 1 : this.index + 1,
			});
		},
		deleteScreen() {
			this.$swal(swalConfig({
				confirmButtonText: 'Usuń ekran',
				html: `Na pewno chcesz usunąć ekran <strong>${this.screen.name}?</strong>`,
				showCancelButton: true,
				type: 'warning',
			}))
				.then(
					() => this.$emit('deleteScreen', this.screen.id),
					() => false
				);
		}
	},
};
</script>
