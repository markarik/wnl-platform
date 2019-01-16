<template>
	<li class="taxonomy-term-item">
		<div :class="['media', 'taxonomy-term-item__content', {'is-selected': isSelected}]">
			<div class="media-content">
				<input class="checkbox" type="checkbox" :checked="isSelected" />
				<span>{{term.tag.name}}</span>
			</div>
			<div class="media-right central">
				<span
					class="icon-small taxonomy-term-item__action"
					@click="toggle"
					v-if="childTerms.length"
				>
					<i :title="chevronTitle" :class="['fa', 'fa-chevron-down', {'fa-rotate-180': isExpanded}]"></i>
				</span>
				<span
					class="icon-small taxonomy-term-item__action"
					@click="edit"
				>
					<i title="Edytuj" class="fa fa-pencil"></i>
				</span>
				<span
					class="icon-small taxonomy-term-item__action"
					@click="add"
				>
					<i title="Dodaj" class="fa fa-plus"></i>
				</span>
			</div>
		</div>
		<transition name="fade">
			<ul v-if="isExpanded && childTerms.length" class="taxonomy-term-item__list">
				<wnl-taxonomy-term-item
					v-for="childTerm in childTerms"
					:key="childTerm.id"
					:term="childTerm"
				>
				</wnl-taxonomy-term-item>
			</ul>
		</transition>
	</li>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.taxonomy-term-item
		&__content
			align-items: center
			border-bottom: 1px solid $color-inactive-gray
			padding: $margin-small 0
		&__list
			margin-left: $margin-big
		&__action
			padding: $margin-small-minus

		.is-selected
			background: $color-ocean-blue-less-opacity

	.fa-chevron-down
		transition: all .1s linear

	.fade-enter-active
		transition: opacity .3s

	.fade-enter,
	.fade-leave-to
		opacity: 0
</style>


<script>
import {mapActions, mapGetters, mapState} from 'vuex';
import {TAXONOMY_EDITOR_MODES} from '../../../consts/taxonomyTerms';

export default {
	props: {
		term: {
			type: Object,
			required: true,
		},
	},
	computed: {
		...mapGetters('taxonomyTerms', ['filteredTerms']),
		...mapState('taxonomyTerms', ['expandedTerms', 'selectedTerms']),
		chevronTitle() {
			return this.isExpanded ? 'Zwiń' : 'Rozwiń';
		},
		childTerms() {
			return this.filteredTerms.filter(term => term.parent_id === this.term.id);
		},
		isSelected() {
			return this.selectedTerms.indexOf(this.term.id) > -1;
		},
		isExpanded() {
			return this.expandedTerms.indexOf(this.term.id) > -1;
		},
	},
	methods: {
		...mapActions('taxonomyTerms', ['collapseTaxonomyTerm', 'expandTaxonomyTerm', 'selectTaxonomyTerms', 'setEditorMode']),
		add() {
			this.setEditorMode(TAXONOMY_EDITOR_MODES.ADD);
			this.selectTaxonomyTerms([this.term.id]);
		},
		edit() {
			this.setEditorMode(TAXONOMY_EDITOR_MODES.EDIT);
			this.selectTaxonomyTerms([this.term.id]);
		},
		toggle() {
			if (this.isExpanded) {
				this.collapseTaxonomyTerm(this.term);
			} else {
				this.expandTaxonomyTerm(this.term);
			}
		},
	},
	// Name is required to allow recursive rendering
	name: 'wnl-taxonomy-term-item',
};
</script>
