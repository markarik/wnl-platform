<template>
	<div>
		<h2 class="title is-2">Edycja struktury kursu</h2>

		<wnl-nested-set-editor :is-loading="isLoading">
			<template slot="header">
				<span class="control has-icons-right structure-editor__search margin bottom">
					<wnl-node-autocomplete
						@change="onSearch"
						placeholder="Szukaj"
					/>
					<span class="icon is-small is-right">
						<i class="fa fa-search"></i>
					</span>
				</span>
			</template>
			<wnl-structure-nodes-list slot="nodesList" :nodes="getRootNodes"/>
			<wnl-structure-node-editor-right slot="panelRight" :courseId="courseId"/>
		</wnl-nested-set-editor>
	</div>
</template>

<style lang="sass" scoped>
	.structure-editor__search
		flex: 1 0 auto
</style>

<script>
import {mapActions, mapState, mapGetters} from 'vuex';

import WnlNestedSetEditor from 'js/admin/components/nestedSet/NestedSetEditor';
import WnlStructureNodesList from 'js/admin/components/structure/StructureNodesList';
import WnlStructureNodeEditorRight from 'js/admin/components/structure/StructureNodeEditorRight';
import WnlNodeAutocomplete from 'js/admin/components/structure/StructureNodeEditorNodeAutocomplete';
import scrollToNodeMixin from 'js/admin/mixins/scroll-to-node';

export default {
	components: {
		WnlStructureNodesList,
		WnlStructureNodeEditorRight,
		WnlNodeAutocomplete,
		WnlNestedSetEditor
	},
	props: {
		courseId: {
			type: [String, Number],
			required: true,
		},
	},
	computed: {
		...mapState('courseStructure', ['isLoading', 'nodes']),
		...mapGetters('courseStructure', ['getChildrenByParentId', 'getRootNodes']),
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('courseStructure', ['setUpNestedSet', 'expandAll', 'focus']),
		async onSearch(node) {
			this.focus(node);
			this.scrollToNode(node);
		},
	},
	mixins: [
		scrollToNodeMixin,
	],
	async mounted() {
		try {
			await this.setUpNestedSet(this.courseId);
		} catch (error) {
			$wnl.logger.capture(error);
			this.addAutoDismissableAlert({
				text: 'Coś poszło nie tak przy pobieraniu struktury',
				type: 'error'
			});
		}
		this.expandAll();
	},
};
</script>
