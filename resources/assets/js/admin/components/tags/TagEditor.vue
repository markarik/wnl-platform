<template>
	<div>
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
		<wnl-paginated-sortable-table
			:isSearchEnabled="false"
			:resourceName="'taggables/.filter'"
			:columns="columns"
			:customRequestParams="requestParams"
		>
			<div slot="header">
				<h3 class="title is-3">Lista elementów powiązanych</h3>
				<div class="filters">
					<div class="field" v-for="(filter, model) in taggableTypeFilters" :key="model">
						<input type="checkbox" :id="`filter${model}`" :value="model" v-model="selectedFilters" class="is-checkradio">
						<label :for="`filter${model}`" class="checkbox">{{filter.label}}</label>
					</div>
				</div>
			</div>
			<tbody slot-scope="slotProps" slot="tbody">
			<tr v-for="taggable in slotProps.list" :key="taggable.id">
				<td>{{taggable.taggable_id}}</td>
				<td>{{taggable.taggable_type}}</td>
				<td>
					<a
						:href="getTaggableLink(taggable)"
						v-if="getTaggableLink(taggable)"
						target="_blank"
					>
						Przejdź do elementu
					</a>
					<span class="table-cell--no-link" v-else>nie umiemy zrobić linka dla tego zasobu</span>
				</td>
			</tr>
			</tbody>
		</wnl-paginated-sortable-table>
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

	.table-cell--no-link
		color: $color-gray
		font-style: italic
		font-size: 12px

	.filters
		display: flex
		flex-wrap: wrap
		margin-bottom: $margin-base
</style>

<script>
import { get } from 'lodash';

import {Form as WnlForm, Text as WnlFormText, Submit as WnlSubmit, Textarea as WnlTextarea} from 'js/components/global/form';
import WnlTagDelete from 'js/admin/components/tags/TagDelete';
import WnlPaginatedSortableTable from 'js/admin/components/lists/PaginatedSortableTable';

export default {
	props: {
		id: {
			type: [String, Number],
			required: true,
		},
	},
	data() {
		return {
			formData: {},
			columns: [
				{
					name: 'taggable_id',
					label: 'Id',
				},
				{
					name: 'taggable_type',
					label: 'Typ elementu',
				},
				{
					label: 'Link',
					name: 'link',
					sortable: false
				},
			],
			taggableTypeFilters: {
				'App\\Models\\Lesson': {
					label: 'Lekcje',
					getLink: (id) => `/app/courses/1/lessons/${id}`
				},
				'App\\Models\\Page': {
					label: 'Strony',
					getLink: () => undefined
				},
				'App\\Models\\Screen': {
					name: 'App\\Models\\Screen',
					label: 'Screeny',
					getLink: () => undefined
				},
				'App\\Models\\Slide': {
					label: 'Slajdy',
					getLink: (id) => `/admin/app/slides/edit?slideId=${id}`
				},
				'App\\Models\\QnaQuestion': {
					name: 'App\\Models\\QnaQuestion',
					label: 'QnaQuestion',
					getLink: () => undefined
				},
				'App\\Models\\QuizQuestion': {
					label: 'QuizQuestion',
					getLink: (id) => `/app/questions/single/${id}`
				},
				'App\\Models\\Annotation': {
					label: 'Anotacje',
					getLink: (id) => `/admin/app/annotations?q=${id}&fields=id`
				},
			},
			selectedFilters: []
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
		requestParams() {
			return {
				filters: [
					{
						taggable: {
							tag_id: this.id,
							taggable_types: this.selectedFilters,
						},
					},
				],
			};
		},
	},
	components: {
		WnlFormText,
		WnlForm,
		WnlSubmit,
		WnlTagDelete,
		WnlTextarea,
		WnlPaginatedSortableTable,
	},
	methods: {
		onChange({formData}) {
			this.formData = this.normalizeFormData(formData);
		},
		onSubmitSuccess(data) {
			if (!this.isEdit) {
				this.$router.push({ name: 'tag-edit', params: { id: data.id } });
			}

			this.formData = this.normalizeFormData(data);
		},
		onTagDeleted() {
			this.$router.push({ name: 'tags' });
		},
		normalizeFormData(formData) {
			return {
				...formData,
				taggables_count: get(formData, `included.taggables_count.${formData.id}.taggables_count`),
				is_delete_allowed: get(formData, `included.meta.${formData.id}.is_delete_allowed`, false),
				is_rename_allowed: get(formData, `included.meta.${formData.id}.is_rename_allowed`, true),
			};
		},
		getTaggableLink({taggable_type, taggable_id}) {
			if (!this.taggableTypeFilters[taggable_type]) return;

			return this.taggableTypeFilters[taggable_type].getLink(taggable_id);
		}
	}
};
</script>
