<template>
	<div>
		<nav class="tabs is-uppercase small">
			<ul>
				<li v-for="mode in modes" :class="mode.key === editorMode ? 'is-active' : ''">
					<a @click="changeEditorMode(mode.key)">
						<span class="icon is-small"><i :class="`fa ${mode.icon}`" aria-hidden="true"></i></span>
						<span>{{mode.label}}</span>
					</a>
				</li>
			</ul>
		</nav>
		<wnl-taxonomy-term-editor-add v-if="editorMode === 'add'" :taxonomyId="taxonomyId" />
		<wnl-taxonomy-term-editor-edit v-if="editorMode === 'edit'" :taxonomyId="taxonomyId" />
	</div>
</template>

<script>
import {mapActions, mapState} from 'vuex';

import WnlTaxonomyTermEditorAdd from 'js/admin/components/taxonomies/TaxonomyTermEditorAdd';
import WnlTaxonomyTermEditorEdit from 'js/admin/components/taxonomies/TaxonomyTermEditorEdit';
import {TAXONOMY_EDITOR_MODES} from 'js/consts/taxonomyTerms';

export default {
	props: {
		taxonomyId: {
			type: String|Number,
			required: true,
		}
	},
	data() {
		return {
			modes: [
				{
					icon: 'fa-plus',
					key: TAXONOMY_EDITOR_MODES.ADD,
					label: 'Dodaj',
				},
				{
					icon: 'fa-pencil',
					key: TAXONOMY_EDITOR_MODES.EDIT,
					label: 'Edytuj'
				},
				{
					icon: 'fa-compress',
					key: TAXONOMY_EDITOR_MODES.MERGE,
					label: 'Połącz'
				},
				{
					icon: 'fa-close',
					key: TAXONOMY_EDITOR_MODES.DELETE,
					label: 'Usuń'
				}
			],
		};
	},
	computed: {
		...mapState('taxonomyTerms', ['editorMode', 'selectedTerms']),
	},
	components: {
		WnlTaxonomyTermEditorAdd,
		WnlTaxonomyTermEditorEdit,
	},
	methods: {
		...mapActions('taxonomyTerms', ['selectTaxonomyTerms', 'setEditorMode']),
		changeEditorMode(mode) {
			if (mode === TAXONOMY_EDITOR_MODES.ADD) {
				this.selectTaxonomyTerms([]);
			}

			this.setEditorMode(mode);
		},
	},
};
</script>
