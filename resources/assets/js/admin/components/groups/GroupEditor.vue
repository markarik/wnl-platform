<template>
	<wnl-form
			:method="method"
			:resource-route="resourceRoute"
			:populate="isEdit"
			name="GroupEditor"
			@submitSuccess="onSubmitSuccess"
			@change="onFormDataChange"
			:attach="({lessons: lessonIds})"
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
		<ol>
			<draggable v-model="lessons" @start="drag=true" @end="drag=false">
				<li
						class="lesson"
						v-for="lesson in lessons"
						:key="lesson.id"
				>
					{{lesson.name}}
				</li>
			</draggable>
		</ol>
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

	.lesson
		border-top: $border-light-gray
		cursor: move
		padding: $margin-base 0
		margin-left: $margin-base
</style>

<script>
	import {Form as WnlForm, Text as WnlFormText, Submit as WnlSubmit} from 'js/components/global/form';

	import draggable from 'vuedraggable';

	export default {
		name: 'GroupEditor',
		props: ['id'],
		data() {
			return {
				lessons: [],
			}
		},
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
			lessonIds(){
				return this.lessons.map(lesson => lesson.id);
			}
		},
		components: {
			WnlFormText,
			WnlForm,
			draggable,
			WnlSubmit,
		},
		methods: {
			onSubmitSuccess(data) {
				if (!this.isEdit) {
					this.$router.push({ name: 'group-edit', params: { id: data.id } })
				}
			},
			onFormDataChange({formData}) {
				if (formData.included) {
					this.lessons = formData.lessons.map(lessonId => formData.included.lessons[lessonId]);
				}
			}
		}
	}
</script>
