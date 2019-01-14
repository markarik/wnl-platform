<template>
	<li>
		{{ term.id }}. {{term.tag.name}}
		<ul v-if="childTerms.length">
			<wnl-taxonomy-term-item
				v-for="childTerm in childTerms"
				:key="childTerm.id"
				:term="childTerm"
			>
			</wnl-taxonomy-term-item>
		</ul>
	</li>
</template>

<script>
import {mapState} from 'vuex';

export default {
	props: {
		term: {
			type: Object,
			required: true,
		},
	},
	computed: {
		childTerms() {
			return this.terms.filter(term => term.parent_id === this.term.id);
		},
		...mapState('taxonomyTerms', ['terms']),
	},
	// Name is required to allow recursive rendering
	name: 'wnl-taxonomy-term-item',
};
</script>
