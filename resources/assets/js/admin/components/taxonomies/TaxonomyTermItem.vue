<template>
	<li class="taxonomy-term-item">
		<div class="media taxonomy-term-item__content">
			<div class="media-content">
				<input class="checkbox" type="checkbox" />
				<span>{{term.tag.name}}</span>
			</div>
			<div class="media-right central">
				<span class="icon-small taxonomy-term-item__action">
					<i title="Edytuj" class="fa fa-pencil"></i>
				</span>
				<span class="icon-small taxonomy-term-item__action">
					<i title="Dodaj" class="fa fa-plus"></i>
				</span>
			</div>
		</div>
		<ul v-if="childTerms.length" class="taxonomy-term-item__list">
			<wnl-taxonomy-term-item
				v-for="childTerm in childTerms"
				:key="childTerm.id"
				:term="childTerm"
			>
			</wnl-taxonomy-term-item>
		</ul>
	</li>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.taxonomy-term-item
		&__content
			align-items: center
			border-bottom: 2px solid $color-inactive-gray
			padding: $margin-small 0
		&__list
			margin-left: $margin-big
		&__action
			padding: $margin-small-minus
</style>


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
