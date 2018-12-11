<template>
	<wnl-form
			:method="method"
			:resource-route="resourceRoute"
			:populate="isEdit"
			name="GroupEditor"
			@submitSuccess="onSubmitSuccess"
			:hideDefaultSubmit="true"
			class="editor"
	>
		<div class="header">
			<h2 class="title is-2">Edycja grupy <span v-if="isEdit">(Id: {{id}})</span></h2>
			<wnl-submit class="submit"/>
		</div>
		<wnl-form-text
				name="name"
				class="margin top bottom"
		>Nazwa</wnl-form-text>

		<h3>Kolejność lekcji</h3>
		<small>Lekcję możesz dodać do grupy z poziomu edycji lekcji</small>
		<wnl-sortable name="lessons">
			<template slot-scope="slotProps">{{slotProps.item.name}}</template>
		</wnl-sortable>
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
	import {Form as WnlForm, Text as WnlFormText, Submit as WnlSubmit, Sortable as WnlSortable} from 'js/components/global/form';


	export default {
		name: 'GroupEditor',
		props: ['id'],
		computed: {
			isEdit() {
				return this.id !== 'new';
			},
			method() {
				return this.isEdit ? 'put' : 'post'
			},
			resourceRoute() {
				return this.isEdit ? `groups/${this.id}?include=lessons` : 'groups?include=lessons';
			},
		},
		components: {
			WnlFormText,
			WnlForm,
			WnlSubmit,
			WnlSortable,
		},
		methods: {
			onSubmitSuccess(data) {
				if (!this.isEdit) {
					this.$router.push({ name: 'group-edit', params: { id: data.id } })
				}
			},
		}
	}
</script>
