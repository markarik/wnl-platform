<template>
	<li>
		{{ term.id }}
		<ul v-if="term.children">
			<wnl-taxonomy-term-item
				v-for="child in term.children"
				:key="child.id"
				:term="child">
			</wnl-taxonomy-term-item>
		</ul>
	</li>
</template>

<script>
export default {
	props: {
		term: {
			type: Object,
			required: true,
		},
	},
	// Name is required to allow recursive rendering
	name: 'wnl-taxonomy-term-item',
};
</script>
