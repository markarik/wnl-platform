<template>
	<th @click="$emit('click')" class="column-name">
		{{label}}
		<span class="icon is-small" v-if="isActive">
			<i :class="['fa', sortDirection === 'asc' ? 'fa-chevron-up': 'fa-chevron-down']"></i>
		</span>
	</th>
</template>

<style lang="sass">
	.column-name
		cursor: pointer
</style>

<script>
export default {
	props: {
		label: {
			type: String,
			required: true,
		},
		isActive: {
			type: Boolean,
			default: false
		},
		sortDirection: {
			type: String,
			default: 'asc',
		},
	},
};
</script>
