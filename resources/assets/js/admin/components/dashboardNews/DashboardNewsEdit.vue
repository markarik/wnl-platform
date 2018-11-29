<template>
	<div>
		<wnl-form
				name="DashboardNewsEditor"
				:populate="isEdit"
				:method="formMethod"
				:resourceRoute="formResourceRoute"
				@submitSuccess="onSubmitSucess"
				@change="onChange"
		>
			<wnl-text name="slug">Slug</wnl-text>
			<wnl-textarea name="message">Treść</wnl-textarea>
			<wnl-datepicker name="start_date" :config="datepickerConfig">Wyświetlaj od</wnl-datepicker>
			<wnl-datepicker name="end_date" :config="datepickerConfig">Wyświetlaj do</wnl-datepicker>
		</wnl-form>

		<h3 class="title is-3">Podgląd</h3>
		<wnl-dashboard-news-content :message="formData.message" :slug="formData.slug" />
	</div>
</template>

<style lang="sass" scoped>
	.notification
		max-width: 900px
</style>

<script>
	import {Form as WnlForm, Text as WnlText, Textarea as WnlTextarea, Datepicker as WnlDatepicker} from 'js/components/global/form'
	import WnlDashboardNewsContent from 'js/components/course/dashboard/DashboardNewsContent'

	export default {
		name: 'DashboardNewsEdit',
		data() {
			return {
				datepickerConfig: {
					altInput: true,
					enableTime: true,
					dateFormat: 'U'
				},
				formData: {}
			}
		},
		props: ['id'],
		components: {
			WnlForm,
			WnlText,
			WnlTextarea,
			WnlDatepicker,
			WnlDashboardNewsContent
		},
		computed: {
			formResourceRoute() {
				return this.isEdit ? `site_wide_messages/${this.id}` : 'site_wide_messages';
			},
			formMethod() {
				return this.isEdit ? 'put' : 'post'
			},
			isEdit() {
				return this.id !== 'new';
			}
		},
		methods: {
			onSubmitSucess(data) {
				if (!this.isEdit) {
					this.$router.push({ name: 'dashboard-news-edit', params: { id: data.id } })
				}
			},
			onChange({formData}) {
				this.formData = formData
			}
		},
	}
</script>
