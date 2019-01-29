<template>
	<div class="editor">
		<div class="editor__panel is-left">
			<div class="editor__panel__header">
				<span class="control has-icons-right search">
					<wnl-node-autocomplete
						@change="onSearch"
						placeholder="Szukaj"
					/>
					<span class="icon is-small is-right">
						<i class="fa fa-search"></i>
					</span>
				</span>
			</div>
			<wnl-structure-nodes-list v-if="!isLoading" :terms="getRootNodes"/>
			<wnl-text-loader v-else />
		</div>
		<div class="editor__panel is-right">
			<wnl-structure-node-editor-right
				:courseId="courseId"
			/>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.editor
		border-top: 1px solid $color-lightest-gray
		display: flex

		&__panel
			flex: 50%

			&.is-left
				border-right: 1px solid $color-lightest-gray
				padding-right: $margin-big

			&.is-right
				padding-left: $margin-big

			&__header
				background-color: $color-white
				display: flex
				justify-content: stretch
				padding-top: $margin-big
				position: sticky
				top: -30px
				z-index: 1

			.search
				width: 100%
</style>

<script>
import {mapActions, mapState, mapGetters} from 'vuex';

import WnlStructureNodesList from 'js/admin/components/structure/StructureNodesList';
import WnlStructureNodeEditorRight from 'js/admin/components/structure/StructureNodeEditorRight';
import WnlNodeAutocomplete from 'js/admin/components/structure/StructureNodeEditorNodeAutocomplete';
import scrollToNodeMixin from 'js/admin/mixins/scroll-to-node';

export default {
	components: {
		WnlStructureNodesList,
		WnlStructureNodeEditorRight,
		WnlNodeAutocomplete
	},
	props: {
		courseId: {
			type: [String, Number],
			required: true,
		},
	},
	computed: {
		...mapState('courseStructure', {
			isLoading: 'isLoading',
			terms: 'terms',
		}),
		...mapGetters('courseStructure', ['getChildrenByParentId', 'getRootNodes']),
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('courseStructure', ['setUpNestedSet', 'select', 'expand', 'expandAll', 'collapseAll']),
		async onSearch(node) {
			this.collapseAll();
			this.select([node.id]);
			this.expand(node.id);

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
