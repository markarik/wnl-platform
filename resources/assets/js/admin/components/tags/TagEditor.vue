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
					:hasTaggable="formData.has_taggable"
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
import {Form as WnlForm, Text as WnlFormText, Submit as WnlSubmit, Textarea as WnlTextarea} from 'js/components/global/form';
import WnlTagDelete from 'js/admin/components/tags/TagDelete.vue';

export default {
	props: ['id'],
	data: () => {
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
			return this.isEdit ? `tags/${this.id}` : 'tags';
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
			this.formData = formData;
		},
		onSubmitSuccess(data) {
			if (!this.isEdit) {
				this.$router.push({ name: 'tag-edit', params: { id: data.id } });
			}
		},
	}
};
</script>
