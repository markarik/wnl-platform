<template>
	<div>
		<wnl-form
				name="DashboardNewsEditor"
				:populate="isEdit"
				:method="formMethod"
				:resourceRoute="formResourceRoute"
				:suppressEnter="true"
				@submitSuccess="onSubmitSucess"
				@change="onChange"
		>
			<wnl-text name="slug">Tytuł</wnl-text>
			<wnl-textarea name="message">Treść</wnl-textarea>
			<p>Możesz użyć następujących parametrów:</p>
			<ul class="message-help">
				<li v-for="argumentKey in Object.keys(messageArguments)">
					<span v-pre>{{</span>{{argumentKey}}<span v-pre>}}</span>
				</li>
			</ul>
			<wnl-datepicker name="start_date" :config="datepickerConfig">Wyświetlaj od</wnl-datepicker>
			<wnl-datepicker name="end_date" :config="datepickerConfig">Wyświetlaj do</wnl-datepicker>
		</wnl-form>

		<h3 class="title is-3">Podgląd</h3>
		<wnl-dashboard-news-content
				:message="formData.message"
				:messageArguments="messageArguments"
				:slug="formData.slug"
		/>
	</div>

</template>

<style lang="sass" scoped>
	.notification
		max-width: 900px

	.message-help
		list-style: disc
		margin-bottom: 10px
		/deep/ li
			margin-left: 30px
</style>

<script>
	import { mapGetters } from 'vuex';
	import {Form as WnlForm, Text as WnlText, Textarea as WnlTextarea, Datepicker as WnlDatepicker} from 'js/components/global/form';
	import WnlDashboardNewsContent from 'js/components/course/dashboard/DashboardNewsContent';

	export default {
		name: 'DashboardNewsEdit',
		data() {
			return {
				datepickerConfig: {
					altInput: true,
					enableTime: true,
					dateFormat: 'U',
					altFormat: 'Y-m-d H:i',
					time_24hr: true,
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
			...mapGetters(['currentUserName']),
			formResourceRoute() {
				return this.isEdit ? `site_wide_messages/${this.id}` : 'site_wide_messages';
			},
			formMethod() {
				return this.isEdit ? 'put' : 'post';
			},
			isEdit() {
				return this.id !== 'new';
			},
			messageArguments() {
				return {
					currentUserName: this.currentUserName
				};
			},
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
