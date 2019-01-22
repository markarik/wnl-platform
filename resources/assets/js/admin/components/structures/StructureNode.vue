<template>
	<div>
		<div>{{ node.structurable_type }} {{ node.structurable_id }}</div>
		<structure-node
				v-for="(node,index) in node.children"
				:node="node"
				:key="index"
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
		mounted() {
			console.log(this.node.structurable_type);
		}
	};
</script>
