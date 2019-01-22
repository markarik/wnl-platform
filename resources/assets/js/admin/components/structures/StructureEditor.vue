<template>
	<div>
		<div class="header">
			<h2 class="title is-2">Edycja struktury kursu</h2>
		</div>

		<structure-node v-for="(node, index) in nodes" :key="index" :node="node"></structure-node>


	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'


</style>

<script>
	import StructureNode from 'js/admin/components/structures/StructureNode';
	import axios from 'axios';
	import {getApiUrl} from 'js/utils/env';

	export default {
		name: 'StructureEditor',
		props: {
			courseId: {
				required: true,
			}
		},
		computed: {},
		components: {
			StructureNode
		},
		data() {
			return {
				nodes: {},
			};
		},
		methods: {
			async fetchStructure() {
				try {
					const response = await axios.get(getApiUrl(`course_structure/${this.courseId}?include=lessons,groups`));
					const {data: {included, ...nodes}} = response;
					this.nodes = nodes;
				} catch (error) {
					$wnl.logger.capture(error);
				}
			}
		},
		mounted() {
			this.fetchStructure();
		}
	};
</script>
