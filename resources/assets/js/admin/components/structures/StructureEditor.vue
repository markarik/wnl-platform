<template>
	<div>
		<div class="header">
			<h2 class="title is-2">Edycja struktury kursu</h2>
		</div>

		<structure-node v-for="node in rootNodes" :key="node.id" :node="node"></structure-node>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.header
		margin-bottom: $margin-medium

</style>

<script>
import StructureNode from 'js/admin/components/structures/StructureNode';
import {mapActions, mapState} from 'vuex';

export default {
	components: {
		StructureNode
	},
	props: {
		courseId: {
			required: true,
			type: [String, Number],
		}
	},
	computed: {
		...mapState('courseStructure', ['nodes']),
		rootNodes() {
			return this.nodes.filter(node => node.parent_id === null);
		},
	},
	methods: {
		...mapActions('courseStructure', ['fetchStructure']),
		...mapActions(['addAutoDismissableAlert']),
	},
	async mounted() {
		try {
			await this.fetchStructure(this.courseId);
		} catch (error) {
			$wnl.logger.capture(error);
			this.addAutoDismissableAlert({
				text: 'Nie udało się pobrać struktury :(',
				type: 'error',
			});
		}
	}
};
</script>
