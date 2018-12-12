<template>
	<div>
		<span v-for="(breadcrumb, key) in breadcrumbs" :key="key" class="breadcrumb-item">
			<router-link class="breadcrumb-link" :to="breadcrumb.to">{{breadcrumb.text}}</router-link>
			<span class="icon is-small"><i class="fa fa-angle-right"></i></span>
		</span>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.breadcrumb-item
		color: $color-inactive-gray

		&:last-child

			.icon
				display: none

	.breadcrumb-link,
	.breadcrumb-link:visited
		color: $color-gray-dimmed
		font-size: $font-size-minus-2

	.breadcrumb-link:hover
		color: $color-gray
</style>

<script>
import { mapGetters } from 'vuex';

export default {
	name: 'Breadcrumbs',
	computed: {
		...mapGetters(['breadcrumbs'])
	},
};
</script>
