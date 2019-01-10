<template>
	<li>
		{{ term.id }}. {{included.tags[term.tags[0]].name}}
		<ul v-if="childTerms.length">
			<wnl-taxonomy-term-item
				v-for="childTerm in childTerms"
				:key="childTerm.id"
				:term="childTerm"
				:included="included"
				:terms="terms"
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
		},
		terms: {
			type: Array,
			required: true,
		}
	},
	computed: {
		childTerms() {
			return this.terms.filter(term => term.parent_id === this.term.id)
		}
	},
	// Name is required to allow recursive rendering
	name: 'wnl-taxonomy-term-item',
};
</script>
