<template>
	<div>
		<div class="header">
			<h2 class="title is-2">Edycja struktury kursu</h2>
		</div>

		<structure-node v-for="(node, index) in rootNodes" :key="index" :node="node"></structure-node>

	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

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
			type: String,
		}
	},
	computed: {
		rootNodes() {
			return this.nodes.filter(node => node.parent_id === null);
		},
		...mapState('courseStructure', {
			nodes: 'nodes'
		})
	},
	components: {
		StructureNode
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
