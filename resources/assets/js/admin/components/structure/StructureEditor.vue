<template>
	<div>
		<h2 class="title is-2">Edycja struktury kursu</h2>

		<wnl-nested-set-editor :is-loading="isLoading">
			<template slot="header">
				<span class="control has-icons-right structure-editor__search margin bottom">
					<wnl-node-autocomplete
						placeholder="Szukaj"
						@change="onSearch"
					/>
					<span class="icon is-small is-right">
						<i class="fa fa-search" />
					</span>
				</span>
			</template>
			<wnl-structure-nodes-list slot="nodes-list" :nodes="getRootNodes" />
			<wnl-structure-node-editor-right slot="panel-right" :course-id="courseId" />
		</wnl-nested-set-editor>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.structure-editor__search
		flex: 1 0 auto

		/deep/ .autocomplete-box .icon
			color: $color-darkest-gray
			height: 1rem
			width: 1rem
			position: relative
</style>

<script>
import { mapActions, mapState, mapGetters } from 'vuex';

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
	mixins: [
		scrollToNodeMixin,
	],
	props: {
		courseId: {
			type: [String, Number],
			required: true,
		},
	},
	computed: {
		...mapState('courseStructure', ['isLoading', 'nodes']),
		...mapGetters('courseStructure', ['getChildrenNodesByParentId', 'getRootNodes']),
	},
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
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('courseStructure', ['setUpNestedSet', 'expandAll', 'focus']),
		async onSearch(node) {
			this.focus(node);
			this.scrollToNode(node);
		},
	},
};
</script>
