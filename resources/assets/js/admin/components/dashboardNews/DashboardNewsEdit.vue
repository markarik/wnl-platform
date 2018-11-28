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
        <wnl-text name="start_date">Wyświetlaj od</wnl-text>
        <wnl-text name="end_date">Zakończ wyświetlanie po</wnl-text>
    </wnl-form>
</template>

<script>
    import {Form as WnlForm, Text as WnlText, Textarea as WnlTextarea} from 'js/components/global/form'

	export default {
		name: 'DashboardNewsEdit',
        props: ['id'],
		components: {
			WnlForm,
			WnlText,
            WnlTextarea,
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
