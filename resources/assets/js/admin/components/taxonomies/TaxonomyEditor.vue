<template>
	<div>
		<wnl-form
			:method="method"
			:resource-route="resourceRoute"
			:populate="isEdit"
			:hideDefaultSubmit="true"
			@submitSuccess="onSubmitSuccess"
			name="TaxonomyEditor"
			class="editor"
		>
			<div class="header">
				<h2 class="title is-2">Edycja taksonomii <span v-if="isEdit">(Id: {{id}})</span></h2>
				<wnl-submit class="submit"/>
			</div>
			<wnl-form-text
				name="name"
				class="margin top bottom"
			>Nazwa</wnl-form-text>
			<wnl-textarea
				name="description"
				class="margin top bottom"
			>Opis</wnl-textarea>
		</wnl-form>
		<h3 class="title is-3">Pojęcia</h3>
		<ul class="content">
			<wnl-taxonomy-term-item v-for="term in terms"
				:term="term"
				:included="included"
				:key="term.id"
			/>
		</ul>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.editor
		max-width: 800px

	.header
		+small-shadow-bottom()
		align-items: flex-start
		display: flex
		justify-content: space-between
		background: $color-white
		margin-bottom: $margin-medium
		padding-top: $margin-small
		position: sticky
		top: -$margin-big
		z-index: 101

		.submit
			width: auto
</style>

<script>
import { get } from 'lodash';

import {Form as WnlForm, Text as WnlFormText, Submit as WnlSubmit, Textarea as WnlTextarea} from 'js/components/global/form';
import WnlTaxonomyTermItem from 'js/admin/components/taxonomies/TaxonomyTermItem';
import {getApiUrl} from '../../../utils/env';

export default {
	props: {
		id: {
			type: String|Number,
			required: true,
		},
	},
	data() {
		return {
			terms: [],
			included: {},
		};
	},
	computed: {
		isEdit() {
			return this.id !== 'new';
		},
		method() {
			return this.isEdit ? 'put' : 'post';
		},
		resourceRoute() {
			return this.isEdit ? `taxonomies/${this.id}` : 'taxonomies';
		},
	},
	components: {
		WnlFormText,
		WnlForm,
		WnlSubmit,
		WnlTextarea,
		WnlTaxonomyTermItem
	},
	methods: {
		onSubmitSuccess(data) {
			if (!this.isEdit) {
				this.$router.push({ name: 'taxonomy-edit', params: { id: data.id } });
			}
		},
	},
	async mounted() {
		if (this.id) {
			// TODO error handling
			const {data} = await axios.get(getApiUrl(`taxonomy_terms/byTaxonomy/${this.id}?include=tags,taxonomies,taxonomy_terms`));
			const {included, ...terms} = data;
			this.terms = terms;
			this.included = included;
		}
	},
};
</script>
