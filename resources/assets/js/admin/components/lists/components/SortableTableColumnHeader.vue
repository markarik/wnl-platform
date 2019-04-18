<template>
	<th class="column-name" @click="$emit('click')">
		{{label}}
		<span v-if="isActive" class="icon is-small">
			<i :class="['fa', sortDirection === 'asc' ? 'fa-chevron-up': 'fa-chevron-down']" />
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
