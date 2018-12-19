<template>
	<wnl-form
		:method="method"
		:resource-route="resourceRoute"
		:populate="isEdit"
		:hideDefaultSubmit="true"
		@change="onChange"
		@submitSuccess="onSubmitSuccess"
		name="TagEditor"
		class="editor"
	>
		<div class="header">
			<h2 class="title is-2">Edycja tagu <span v-if="isEdit">(Id: {{id}})</span></h2>
			<div class="field is-grouped">
				<wnl-tag-delete
					:id="id"
					:isDeleteAllowed="formData.is_delete_allowed"
					:taggablesCount="formData.taggables_count"
					@tagDeleted="onTagDeleted"
				>Usuń</wnl-tag-delete>
				<wnl-submit class="submit"/>
				</div>
		</div>
		<wnl-form-text
			name="name"
			class="margin top bottom"
			:disabled="!formData.is_rename_allowed"
		>Nazwa</wnl-form-text>
		<wnl-form-text
			name="color"
			class="margin top bottom"
		>Kolor (RRGGBB)</wnl-form-text>
		<wnl-textarea
			name="description"
			class="margin top bottom"
		>Opis</wnl-textarea>
	</wnl-form>
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
import WnlTagDelete from 'js/admin/components/tags/TagDelete';

export default {
	props: {
		id: {
			type: String|Number,
			required: true,
		},
	},
	data() {
		return {
			formData: {}
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
			return this.isEdit ? `tags/${this.id}?include=taggables_count,meta` : 'tags';
		},
	},
	components: {
		WnlFormText,
		WnlForm,
		WnlSubmit,
		WnlTagDelete,
		WnlTextarea,
	},
	methods: {
		onChange({formData}) {
			this.formData = {
				...formData,
				taggables_count: get(formData, `included.taggables_count.${formData.id}.taggables_count`),
				is_delete_allowed: get(formData, `included.meta.${formData.id}.is_delete_allowed`, false),
				is_rename_allowed: get(formData, `included.meta.${formData.id}.is_rename_allowed`, true),
			};
		},
		onSubmitSuccess(data) {
			if (!this.isEdit) {
				this.$router.push({ name: 'tag-edit', params: { id: data.id } });
			}

			this.formData = data;
		},
		onTagDeleted() {
			this.$router.push({ name: 'tags' });
		},
	}
};
</script>
