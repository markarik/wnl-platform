<template>
	<div class="progress-container">
		<progress class="progress progress-bar is-large" :class="modifyingClass" :value="value" :max="maxValue">
			{{percentageValue}}
		</progress>
		<span v-if="hasNumbers" class="progress-numbers margin left" :class="modifyingClass">
			{{value}}/{{max}}
		</span>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.progress-container
		display: flex

	.progress
		margin-bottom: $margin-small
		width: auto

	.progress-bar
		flex: 1 0 auto

	.progress-numbers
		flex: 0 0 auto
		font-weight: $font-weight-black

		&.is-success
			color: $success

		&.is-warning
			color: $warning

		&.is-danger
			color: $danger
</style>

<script>
export default {
	name: 'Progress',
	props: ['value', 'max', 'hasNumbers', 'modifyingClass'],
	computed: {
		maxValue() {
			return this.max || 100;
		},
		percentageValue() {
			return `${Math.round(this.value/this.max)}%`;
		},
	},
};
</script>
