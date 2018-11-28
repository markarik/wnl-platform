<template>
    <wnl-form
            name="DashboardNewsEditor"
            :populate="isEdit"
            :method="formMethod"
            :resourceRoute="formResourceRoute"
            @submitSuccess="onSubmitSucess"
    >
        <wnl-text name="slug">Slug</wnl-text>
        <wnl-textarea name="message">Treść</wnl-textarea>
        <wnl-datepicker name="start_date" :config="datepickerConfig">Wyświetlaj od</wnl-datepicker>
        <wnl-datepicker name="end_date" :config="datepickerConfig">Wyświetlaj do</wnl-datepicker>
    </wnl-form>
</template>

<script>
    import {Form as WnlForm, Text as WnlText, Textarea as WnlTextarea, Datepicker as WnlDatepicker} from 'js/components/global/form'

	export default {
		name: 'DashboardNewsEdit',
        data() {
			return {
				datepickerConfig: {
					altInput: true,
					enableTime: true,
					dateFormat: 'U'
				},
            }
        },
        props: ['id'],
		components: {
			WnlForm,
			WnlText,
            WnlTextarea,
			WnlDatepicker
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
            }
		},
		mounted() {
		}
	}
</script>
