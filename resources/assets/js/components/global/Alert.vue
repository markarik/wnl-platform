<template>
	<transition name="fade">
		<div class="notification" :class="fullCssClass">
			<button class="delete" @click.prevent="onDelete" />
			<span v-html="alert.message" />
		</div>
	</transition>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.notification
		text-align: center

		&.absolute,
		&.fixed
			border-radius: 0
			bottom: auto
			font-size: $font-size-plus-1
			font-weight: $font-weight-bold
			left: 0
			right: 0
			top: 0
			z-index: $z-index-alerts

		&.absolute
			position: absolute

		&.fixed
			position: fixed

		&.bottom
			bottom: 0
			top: auto
</style>

<script>
export default {
	name: 'Alert',
	props: ['timestamp', 'alert', 'cssClass'],
	computed: {
		fullCssClass() {
			return `${this.alert.cssClass} ${this.cssClass}`;
		},
	},
	methods: {
		onDelete() {
			this.$emit('delete', this.timestamp);
		}
	}
};
</script>
