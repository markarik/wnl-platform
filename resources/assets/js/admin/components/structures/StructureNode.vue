<template>
	<div>
		<!--<div>{{ node.structurable_type }} {{ node.structurable_id }}</div>-->
		<div v-if="node.structurable_type === 'App\\Models\\Group'">{{ node.group.name }}</div>
		<div v-if="node.structurable_type === 'App\\Models\\Lesson'">{{ node.lesson.name }}</div>
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

	.child-node
		margin-left: $margin-big
</style>

<script>
	import StructureNode from 'js/admin/components/structures/StructureNode';
	import {mapState} from 'vuex';

	export default {
		name: "StructureNode",
		props: {
			node: {
				type: Object,
				required: true,
			}
		},
		components: {
			StructureNode,
		},
		computed: {
			...mapState('structureNodes', {
				nodes: 'nodes'
			}),
			childNodes() {
				return this.nodes.filter(node => node.parent_id === this.node.id);
			}
		},
		methods: {},
		mounted() {
		}
	};
</script>
