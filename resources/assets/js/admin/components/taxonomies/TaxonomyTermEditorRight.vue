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

import WnlTaxonomyTermEditorAdd from 'js/admin/components/taxonomies/TaxonomyTermEditorAdd';
import WnlTaxonomyTermEditorEdit from 'js/admin/components/taxonomies/TaxonomyTermEditorEdit';
import {TAXONOMY_EDITOR_MODES} from 'js/consts/taxonomyTerms';

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
					key: TAXONOMY_EDITOR_MODES.ADD,
					label: 'Dodaj',
					componentName: WnlTaxonomyTermEditorAdd
				},
				{
					icon: 'fa-pencil',
					key: TAXONOMY_EDITOR_MODES.EDIT,
					label: 'Edytuj',
					componentName: WnlTaxonomyTermEditorEdit
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
		activeMode() {
			return this.modes.find(mode => mode.key === this.editorMode);
		}
	},
	methods: {
		...mapActions('taxonomyTerms', {
			selectTerms: 'select',
			setEditorMode: 'setEditorMode',
		}),
	},
};
</script>
