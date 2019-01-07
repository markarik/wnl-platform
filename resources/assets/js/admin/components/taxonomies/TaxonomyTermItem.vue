<template>
	<li>
		{{ term.id }}. {{included.tags[term.tags[0]].name}}
		<ul v-if="term.taxonomy_terms">
			<wnl-taxonomy-term-item
				v-for="termId in term.taxonomy_terms"
				:key="termId"
				:term="included.taxonomy_terms[termId]"
				:included="included"
			>
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
		included: {
			type: Object,
			required: true,
		}
	},
	// Name is required to allow recursive rendering
	name: 'wnl-taxonomy-term-item',
};
</script>
