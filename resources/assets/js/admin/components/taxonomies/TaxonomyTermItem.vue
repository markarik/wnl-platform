<template>
	<li class="taxonomy-term-item">
		<div class="media taxonomy-term-item__content">
			<div class="media-content">
				<input class="checkbox" type="checkbox" />
				<span>{{term.tag.name}}</span>
			</div>
			<div class="media-right central">
				<span
					class="icon-small taxonomy-term-item__action"
					@click="showChildren = !showChildren"
					v-if="childTerms.length"
				>
					<i :title="chevronTitle" :class="chevronClass"></i>
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
			<ul v-if="showChildren && childTerms.length" class="taxonomy-term-item__list">
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
			border-bottom: 2px solid $color-inactive-gray
			padding: $margin-small 0
		&__list
			margin-left: $margin-big
		&__action
			padding: $margin-small-minus

	.fa-chevron-down
		transition: all .1s linear

	.fade-enter-active
		transition: opacity .3s

	.fade-enter,
	.fade-leave-to
		opacity: 0
</style>


<script>
import {mapActions, mapGetters} from 'vuex';
import {TAXONOMY_EDITOR_MODES} from '../../../consts/taxonomyTerms';

export default {
	props: {
		term: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {
			showChildren: false,
		};
	},
	computed: {
		...mapGetters('taxonomyTerms', ['filteredTerms']),
		chevronClass() {
			const classes = ['fa', 'fa-chevron-down'];

			if (this.showChildren) {
				classes.push('fa-rotate-180');
			}

			return classes.join(' ');
		},
		chevronTitle() {
			return this.showChildren ? 'Zwiń' : 'Rozwiń';
		},
		childTerms() {
			return this.filteredTerms.filter(term => term.parent_id === this.term.id);
		},
	},
	methods: {
		...mapActions('taxonomyTerms', ['selectTaxonomyTerms', 'setEditorMode']),
		edit() {
			this.setEditorMode(TAXONOMY_EDITOR_MODES.EDIT);
			this.selectTaxonomyTerms([this.term.id]);
		},
		add() {
			this.setEditorMode(TAXONOMY_EDITOR_MODES.ADD);
			this.selectTaxonomyTerms([this.term.id]);
		}
	},
	// Name is required to allow recursive rendering
	name: 'wnl-taxonomy-term-item',
};
</script>
