<template>
	<wnl-form
			:method="method"
			:resource-route="resourceRoute"
			:populate="isEdit"
			name="CourseEditor"
			:hide-default-submit="true"
			class="editor"
	>
		<div class="header">
			<h2 class="title is-2">Edycja kursu <span v-if="isEdit">(Id: {{id}})</span></h2>
			<wnl-submit class="submit"/>
		</div>
		<wnl-form-text
				name="name"
				class="margin vertical"
		>Nazwa</wnl-form-text>
		<wnl-form-check
			name="is_plan_builder_enabled"
			class="margin vertical"
		>Włącz edytor planu pracy</wnl-form-check>
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
import {Form as WnlForm, Text as WnlFormText, Check as WnlFormCheck, Submit as WnlSubmit} from 'js/components/global/form';

export default {
	name: 'CourseEditor',
	props: ['id'],
	computed: {
		isEdit() {
			return this.id !== 'new';
		},
		method() {
			return this.isEdit ? 'put' : 'post';
		},
		resourceRoute() {
			return this.isEdit ? `courses/${this.id}` : 'courses';
		},
	},
	components: {
		WnlFormText,
		WnlFormCheck,
		WnlForm,
		WnlSubmit,
	},
	methods: {
		onSubmitSuccess(data) {
			if (!this.isEdit) {
				this.$router.push({ name: 'course-edit', params: { id: data.id } });
			}
		},
	}
};
</script>
