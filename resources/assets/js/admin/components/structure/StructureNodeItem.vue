<template>
	<li :class="['structure-node-item', isSaving && 'structure-node-item is-disabled']" :id="`node-${node.id}`">
		<div :class="['media', 'structure-node-item__content', {'is-selected': isSelected}]">
			<span class="icon-small structure-node-item__icon structure-node-item__action__drag">
				<i title="drag" :class="['fa', isSaving ? 'fa-circle-o-notch fa-spin' : 'fa-bars']"></i>
			</span>
			<span class="icon-small structure-node-item__icon">
				<i :class="['fa', getStructurableIcon(node.structurable)]"></i>
			</span>
			<div class="media-content v-central">
				<span>{{node.structurable.name}}</span>
			</div>
			<div class="media-right central">
				<span
					class="icon-small structure-node-item__action"
					@click="toggle"
					v-if="childNodes.length"
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
			</div>
		</div>
		<transition name="fade">
			<wnl-structure-nodes-list
				v-if="isExpanded"
				class="structure-node-item__list"
				:nodes="childNodes"
			/>
		</transition>
	</li>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.structure-node-item
		.is-disabled
			pointer-events: none
			color: $color-darkest-gray

		&__content
			align-items: center
			border-bottom: 1px solid $color-inactive-gray
			padding: $margin-small 0

		&__list
			margin-left: $margin-big

		&__action
			cursor: pointer

		&__action, &__icon
			margin: 0 $margin-tiny
			padding: $margin-small-minus

			&__drag
				cursor: move
				color: $color-inactive-gray

			.is-disabled
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
		node: {
			type: Object,
			required: true,
		},
	},
	computed: {
		...mapState('courseStructure', ['expandedNodes', 'selectedNodes', 'isSaving']),
		...mapGetters('courseStructure', ['getChildrenNodesByParentId', 'getStructurableIcon']),
		chevronTitle() {
			return this.isExpanded ? 'Zwiń' : 'Rozwiń';
		},
		childNodes() {
			return this.getChildrenNodesByParentId(this.node.id);
		},
		isSelected() {
			return this.selectedNodes.includes(this.node.id);
		},
		isExpanded() {
			return this.expandedNodes.includes(this.node.id)  && this.childNodes.length;
		},
	},
	methods: {
		...mapActions('courseStructure', ['setEditorMode']),
		...mapActions('courseStructure', {
			'collapseNode': 'collapse',
			'expandNode': 'expand',
			'selectNodes': 'select',
		}),
		onAdd() {
			this.setEditorMode(NESTED_SET_EDITOR_MODES.ADD);
			this.selectNodes([this.node.id]);
		},
		onDelete() {
			this.setEditorMode(NESTED_SET_EDITOR_MODES.DELETE);
			this.selectNodes([this.node.id]);
		},
		onEdit() {
			this.setEditorMode(NESTED_SET_EDITOR_MODES.EDIT);
			this.selectNodes([this.node.id]);
		},
		toggle() {
			if (this.isExpanded) {
				this.collapseNode(this.node.id);
			} else {
				this.expandNode(this.node.id);
			}
		},
	},
	beforeCreate: function () {
		// https://vuejs.org/v2/guide/components-edge-cases.html#Circular-References-Between-Components
		this.$options.components.WnlStructureNodesList = require('./StructureNodesList.vue').default;
	}
};
</script>
