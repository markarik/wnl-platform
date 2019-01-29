<template>
	<li :class="['structure-node-item', isSaving && 'structure-node-item--disabled']" :id="`term-${term.id}`">
		<div :class="['media', 'structure-node-item__content', {'is-selected': isSelected}]">
			<span class="icon-small structure-node-item__action structure-node-item__action--drag">
				<i title="drag" :class="['fa', itemClass]"></i>
			</span>
			<div class="media-content v-central">
				<span>{{term.structurable.name}}</span>
			</div>
			<div class="media-right central">
				<span
					class="icon-small structure-node-item__action"
					@click="toggle"
					v-if="childTerms.length"
				>
					<i :title="chevronTitle" :class="['fa', 'fa-chevron-down', {'fa-rotate-180': isExpanded}]"></i>
				</span>
				<span
					class="icon-small structure-node-item__action"
					@click="onAdd"
				>
					<i title="Dodaj" class="fa fa-plus"></i>
				</span>
				<span
					class="icon-small structure-node-item__action"
					@click="onEdit"
				>
					<i title="Edytuj" class="fa fa-pencil"></i>
				</span>
				<span
					class="icon-small structure-node-item__action"
					@click="onDelete"
				>
					<i title="Usuń" class="fa fa-trash"></i>
				</span>
				<span
					:class="['icon-small', 'structure-node-item__action', {'structure-node-item__action--disabled': !canBeMovedUp}]"
					@click="canBeMovedUp && onTermMove(term, -1)"
				>
					<i title="Do góry" class="fa fa-arrow-up"></i>
				</span>
				<span
					class="icon-small structure-node-item__action"
					:class="['icon-small', 'structure-node-item__action', {'structure-node-item__action--disabled': !canBeMovedDown}]"
					@click="canBeMovedDown && onTermMove(term, 1)"
				>
					<i title="Na dół" class="fa fa-arrow-down"></i>
				</span>
			</div>
		</div>
		<transition name="fade">
			<wnl-structure-nodes-list
				v-if="isExpanded"
				class="structure-node-item__list"
				:terms="childTerms"
			/>
		</transition>
	</li>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.structure-node-item
		&--disabled
			pointer-events: none
			color: $color-gray-dimmed

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
import {mapActions, mapState, mapGetters} from 'vuex';
import {NESTED_SET_EDITOR_MODES} from 'js/consts/nestedSet';
import {COURSE_STRUCTURE_TYPES} from 'js/consts/courseStructure';

export default {
	props: {
		term: {
			type: Object,
			required: true,
		},
	},
	computed: {
		...mapState('courseStructure', ['expandedTerms', 'selectedTerms', 'isSaving']),
		...mapGetters('courseStructure', ['getChildrenByParentId']),
		canBeMovedUp() {
			return this.term.orderNumber > 0;
		},
		canBeMovedDown() {
			return this.term.orderNumber < this.getChildrenByParentId(this.term.parent_id).length - 1;
		},
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
		itemClass() {
			if (this.isSaving) return 'fa-circle-o-notch fa-spin';
			if (this.term.structurable_type === COURSE_STRUCTURE_TYPES.LESSON) return 'fa-book';
			return 'fa-folder';
		}
	},
	methods: {
		...mapActions('courseStructure', ['setEditorMode']),
		...mapActions('courseStructure', {
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
		onTermMove(term, direction) {
			this.$emit('moveTerm', {term, direction});
		},
	},
	beforeCreate: function () {
		// https://vuejs.org/v2/guide/components-edge-cases.html#Circular-References-Between-Components
		this.$options.components.WnlStructureNodesList = require('./StructureNodesList.vue').default;
	}
};
</script>
