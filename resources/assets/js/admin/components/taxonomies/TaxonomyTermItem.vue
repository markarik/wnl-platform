<template>
	<li :id="`node-${term.id}`" :class="['taxonomy-term-item', isSaving && 'taxonomy-term-item--disabled']">
		<div :class="['media', 'taxonomy-term-item__content', {'is-selected': isSelected}]">
			<span class="icon-small taxonomy-term-item__action taxonomy-term-item__action--drag">
				<i title="drag" :class="['fa', isSaving ? 'fa-circle-o-notch fa-spin' : 'fa-bars']" />
			</span>
			<div class="media-content v-central">
				<span>{{term.tag.name}}</span>
			</div>
			<div class="media-right central">
				<span
					v-if="childTerms.length"
					class="icon-small taxonomy-term-item__action"
					@click="toggle"
				>
					<i :title="chevronTitle" :class="['fa', 'fa-chevron-down', {'fa-rotate-180': isExpanded}]" />
				</span>
				<span
					class="icon-small taxonomy-term-item__action"
					@click="onAdd"
				>
					<i title="Dodaj" class="fa fa-plus" />
				</span>
				<span
					class="icon-small taxonomy-term-item__action"
					@click="onEdit"
				>
					<i title="Edytuj" class="fa fa-pencil" />
				</span>
				<span
					class="icon-small taxonomy-term-item__action"
					@click="onDelete"
				>
					<i title="Usuń" class="fa fa-trash" />
				</span>
			</div>
		</div>
		<transition name="fade">
			<wnl-taxonomy-terms-list
				v-if="isExpanded"
				class="taxonomy-term-item__list"
				:terms="childTerms"
			/>
		</transition>
	</li>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.taxonomy-term-item
		&--disabled
			pointer-events: none
			color: $color-gray

		&__content
			align-items: center
			border-bottom: 1px solid $color-inactive-gray
			padding: $margin-small 0

		&__list
			margin-left: $margin-big

		&__action
			cursor: pointer
			margin: 0 $margin-tiny
			padding: $margin-small-minus

			&--drag
				cursor: move

			&--disabled
				color: $color-inactive-gray
				cursor: not-allowed

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
import { mapActions, mapState, mapGetters } from 'vuex';
import { NESTED_SET_EDITOR_MODES } from 'js/consts/nestedSet';

export default {
	props: {
		term: {
			type: Object,
			required: true,
		},
	},
	computed: {
		...mapState('taxonomyTerms', {
			expandedTerms: 'expandedNodes',
			selectedTerms: 'selectedNodes',
			isSaving: 'isSaving',
		}),
		...mapGetters('taxonomyTerms', ['getChildrenNodesByParentId']),
		chevronTitle() {
			return this.isExpanded ? 'Zwiń' : 'Rozwiń';
		},
		childTerms() {
			return this.getChildrenNodesByParentId(this.term.id);
		},
		isSelected() {
			return this.selectedTerms.includes(this.term.id);
		},
		isExpanded() {
			return this.expandedTerms.includes(this.term.id)  && this.childTerms.length;
		},
	},
	methods: {
		...mapActions('taxonomyTerms', ['setEditorMode']),
		...mapActions('taxonomyTerms', {
			'collapseTerm': 'collapse',
			'expandTerm': 'expand',
			'selectTerms': 'select',
		}),
		onAdd() {
			this.setEditorMode(NESTED_SET_EDITOR_MODES.ADD);
			this.selectTerms([this.term.id]);
		},
		onDelete() {
			this.setEditorMode(NESTED_SET_EDITOR_MODES.DELETE);
			this.selectTerms([this.term.id]);
		},
		onEdit() {
			this.setEditorMode(NESTED_SET_EDITOR_MODES.EDIT);
			this.selectTerms([this.term.id]);
		},
		toggle() {
			if (this.isExpanded) {
				this.collapseTerm(this.term.id);
			} else {
				this.expandTerm(this.term.id);
			}
		},
	},
	beforeCreate: function () {
		// https://vuejs.org/v2/guide/components-edge-cases.html#Circular-References-Between-Components
		this.$options.components.WnlTaxonomyTermsList = require('./TaxonomyTermsList.vue').default;
	}
};
</script>
