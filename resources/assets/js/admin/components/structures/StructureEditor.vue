<template>
	<div class="structure-tree">
		<div class="header">
			<h2 class="title is-2">Edycja struktury kursu</h2>
		</div>

		<structure-node v-for="(node, index) in rootNodes" :key="index" :node="node"></structure-node>

	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'


	.structure-tree
		bottom: $margin-big
		display: flex
		flex-direction: column
		left: $margin-big
		position: absolute
		right: $margin-big
		top: $margin-big

</style>

<script>
	import StructureNode from 'js/admin/components/structures/StructureNode';
	import {mapActions, mapState} from 'vuex';
	import {getApiUrl} from 'js/utils/env';

	export default {
		name: 'StructureEditor',
		props: {
			courseId: {
				required: true,
			}
		},
		computed: {
			rootNodes() {
				return this.nodes.filter(node => node.parent_id === null);
			},
			...mapState('structureNodes', {
				nodes: 'nodes'
			})
		},
		components: {
			StructureNode
		},
		methods: {
			...mapActions('structureNodes', ['fetchStructure'])
		},
		mounted() {
			this.fetchStructure(this.courseId);
		}
	};
</script>
