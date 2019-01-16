<template>
	<div>
		<nav class="tabs is-uppercase small">
			<ul>
				<li v-for="mode in modes" :class="mode.key === editorMode ? 'is-active' : ''">
					<a @click="setEditorMode(mode.key)">
						<span class="icon is-small"><i :class="`fa ${mode.icon}`" aria-hidden="true"></i></span>
						<span>{{mode.label}}</span>
					</a>
				</li>
			</ul>
		</nav>
		<wnl-taxonomy-term-editor-add v-if="editorMode === 'add'" />
		<wnl-taxonomy-term-editor-edit v-if="editorMode === 'edit'" />
	</div>
</template>

<script>
import {mapActions, mapState} from 'vuex';

import WnlTaxonomyTermEditorAdd from 'js/admin/components/taxonomies/TaxonomyTermEditorAdd';
import WnlTaxonomyTermEditorEdit from 'js/admin/components/taxonomies/TaxonomyTermEditorEdit';

export default {
	props: {
	},
	data() {
		return {
			modes: [
				{
					icon: 'fa-plus',
					key: 'add',
					label: 'Dodaj',
				},
				{
					icon: 'fa-pencil',
					key: 'edit',
					label: 'Edytuj'
				},
				{
					icon: 'fa-compress',
					key: 'merge',
					label: 'Połącz'
				},
				{
					icon: 'fa-close',
					key: 'delete',
					label: 'Usuń'
				}
			],
		};
	},
	computed: {
		...mapState('taxonomyTerms', ['editorMode']),
	},
	components: {
		WnlTaxonomyTermEditorAdd,
		WnlTaxonomyTermEditorEdit,
	},
	methods: {
		...mapActions('taxonomyTerms', ['setEditorMode']),
	},
};
</script>
