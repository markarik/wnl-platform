<template>
	<div class="structure-node">
		<component :is="structurableComponent" :structurable="node.structurable"></component>
		<structure-node
				v-for="childNode in childNodes"
				:key="childNode.id"
				:node="childNode"
				class="child-node">
		</structure-node>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.structure-node

		.child-node
			margin-left: $margin-big
</style>

<script>
import StructureNode from 'js/admin/components/structures/StructureNode';
import GroupNodeElement from 'js/admin/components/structures/GroupNodeElement';
import LessonNodeElement from 'js/admin/components/structures/LessonNodeElement';
import {mapState} from 'vuex';

export default {
	name: 'StructureNode',
	props: {
		node: {
			type: Object,
			required: true,
		}
	},
	components: {
		StructureNode,
		GroupNodeElement,
		LessonNodeElement,
	},
	data() {
		return {
			typeComponentMap: {
				'App\\Models\\Group': 'GroupNodeElement',
				'App\\Models\\Lesson': 'LessonNodeElement',
			}
		};
	},
	computed: {
		...mapState('courseStructure', {
			nodes: 'nodes'
		}),
		structurableComponent() {
			return this.typeComponentMap[this.node.structurable_type];
		},
		childNodes() {
			return this.nodes.filter(node => node.parent_id === this.node.id);
		}
	},
	methods: {},
	mounted() {
	}
};
</script>
