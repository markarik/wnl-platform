<template>
	<li class="taxonomy-term-item" :id="`term-${term.id}`">
		<div :class="['media', 'taxonomy-term-item__content', {'is-selected': isSelected}]">
			<span
				class="icon-small taxonomy-term-item__action"
			>
				<i title="drag" class="fa fa-bars"></i>
			</span>
			<div class="media-content v-central">
				<input class="checkbox margin right" type="checkbox" :checked="isSelected" />
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
					@click="add"
				>
					<i title="Dodaj" class="fa fa-plus"></i>
				</span>
				<span
					class="icon-small taxonomy-term-item__action"
					@click="edit"
				>
					<i title="Edytuj" class="fa fa-pencil"></i>
				</span>
				<span class="icon-small taxonomy-term-item__action" @click="onTermMove(term, -1)">
					<i title="Do góry" class="fa fa-arrow-up"></i>
				</span>
				<span class="icon-small taxonomy-term-item__action" @click="onTermMove(term, 1)">
					<i title="Na dół" class="fa fa-arrow-down"></i>
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
		&__content
			cursor: move
			align-items: center
			border-bottom: 1px solid $color-inactive-gray
			padding: $margin-small 0 $margin-small $margin-base
		&__list
			margin-left: $margin-big
		&__action
			cursor: pointer
			margin: 0 $margin-tiny
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
import {mapActions, mapState, mapGetters} from 'vuex';
import {TAXONOMY_EDITOR_MODES} from 'js/consts/taxonomyTerms';

export default {
	props: {
		term: {
			type: Object,
			required: true,
		},
	},
	computed: {
		...mapState('taxonomyTerms', ['terms', 'expandedTerms', 'selectedTerms']),
		...mapGetters('taxonomyTerms', ['getChildrenByParentId']),
		chevronTitle() {
			return this.isExpanded ? 'Zwiń' : 'Rozwiń';
		},
		childTerms() {
			return this.getChildrenByParentId(this.term.id);
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
		add() {
			this.setEditorMode(TAXONOMY_EDITOR_MODES.ADD);
			this.selectTerms([this.term.id]);
		},
		edit() {
			this.setEditorMode(TAXONOMY_EDITOR_MODES.EDIT);
			this.selectTerms([this.term.id]);
		},
		toggle() {
			if (this.isExpanded) {
				this.collapseTerm(this.term.id);
			} else {
				this.expandTerm(this.term.id);
			}
		},
		onTermMove(term, direction) {
			this.$emit('moveTerm', {term, direction});
		},
	},
	beforeCreate: function () {
		// https://vuejs.org/v2/guide/components-edge-cases.html#Circular-References-Between-Components
		this.$options.components.WnlTaxonomyTermsList = require('./TaxonomyTermsList.vue').default;
	}
};
</script>
