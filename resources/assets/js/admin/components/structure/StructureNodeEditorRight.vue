<template>
	<div class="terms-editor-right">
		<nav class="tabs is-uppercase small">
			<ul>
				<li v-for="mode in modes" :class="{'is-active': mode.key === editorMode}">
					<a @click="setEditorMode(mode.key)">
						<span class="icon is-small"><i :class="['fa', mode.icon]" aria-hidden="true"></i></span>
						<span>{{mode.label}}</span>
					</a>
				</li>
			</ul>
		</nav>

		<component :is="activeMode.componentName" :taxonomyId="taxonomyId" />
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.terms-editor-right
		padding-top: $margin-big
		position: sticky
		top: -30px
</style>

<script>
import {mapActions, mapState} from 'vuex';

import WnlStructureNodeEditorAdd from 'js/admin/components/structure/StructureNodeEditorAdd';
import WnlStructureNodeEditorDelete from 'js/admin/components/structure/StructureNodeEditorDelete';
import WnlStructureNodeEditorEdit from 'js/admin/components/structure/StructureNodeEditorEdit';
import {NESTED_SET_EDITOR_MODES} from 'js/consts/nestedSet';

export default {
	props: {
		taxonomyId: {
			type: [String, Number],
			required: true,
		}
	},
	data() {
		return {
			modes: [
				{
					icon: 'fa-plus',
					key: NESTED_SET_EDITOR_MODES.ADD,
					label: 'Dodaj',
					componentName: WnlStructureNodeEditorAdd
				},
				{
					icon: 'fa-pencil',
					key: NESTED_SET_EDITOR_MODES.EDIT,
					label: 'Edytuj',
					componentName: WnlStructureNodeEditorEdit
				},
				// {
				// 	icon: 'fa-compress',
				// 	key: NESTED_SET_EDITOR_MODES.MERGE,
				// 	label: 'Połącz'
				// },
				{
					icon: 'fa-trash',
					key: NESTED_SET_EDITOR_MODES.DELETE,
					label: 'Usuń',
					componentName: WnlStructureNodeEditorDelete
				}
			],
		};
	},
	computed: {
		...mapState('courseStructure', ['editorMode', 'selectedTerms']),
		activeMode() {
			return this.modes.find(mode => mode.key === this.editorMode);
		}
	},
	methods: {
		...mapActions('courseStructure', {
			selectTerms: 'select',
			setEditorMode: 'setEditorMode',
		}),
	},
};
</script>
