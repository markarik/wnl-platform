<template>
	<div>
		<wnl-form
				name="DashboardNewsEditor"
				:populate="isEdit"
				:method="formMethod"
				:resource-route="formResourceRoute"
				suppress-enter
				@submitSuccess="onSubmitSucess"
				@change="onChange"
		>
			<wnl-text name="slug">Tytuł</wnl-text>
			<wnl-textarea name="message">Treść</wnl-textarea>
			<p class="message-links-info">
				Jeżeli chcesz umieścić link do strony wewnątrz platformy, użyj relatywnego adresu.
				Na przykład: <code>/app/courses/1</code> zamiast <code>https://platforma.wiecejnizlek.pl/app/courses/1</code>.
				Pamiętaj o ukośniku <code>/</code> na początku adresu!
				Dzięki temu strona otworzy się szybciej i bez przeładowania całego widoku.
			</p>
			<p>Możesz użyć następujących parametrów:</p>
			<ul class="message-arguments">
				<li
						class="message-argument"
						v-for="(value, key) in messageArguments" :key="key"
				>
					<code>{{escapeArgumentKey(key)}}</code> - {{value.description}}
				</li>
			</ul>
			<wnl-datepicker name="start_date" :config="datepickerConfig">Wyświetlaj od</wnl-datepicker>
			<wnl-datepicker name="end_date" :config="datepickerConfig">Wyświetlaj do</wnl-datepicker>
		</wnl-form>

		<h3 class="title is-3">Podgląd</h3>
		<wnl-dashboard-news-content
				:message="formData.message"
				:message-arguments="messageArguments"
				:slug="formData.slug"
		/>
	</div>

</template>

<style lang="sass" scoped>
	.notification
		max-width: 900px

	.message-links-info
		margin-bottom: 10px

	.message-arguments
		list-style: disc
		margin-bottom: 10px

	.message-argument
		margin-left: 30px
</style>

<script>
import {Form as WnlForm, Text as WnlText, Textarea as WnlTextarea, Datepicker as WnlDatepicker} from 'js/components/global/form';
import WnlDashboardNewsContent from 'js/components/course/dashboard/DashboardNewsContent';
import dashboardNewsMessageArguments from 'js/mixins/dashboard-news-message-arguments';

export default {
	name: 'DashboardNewsEdit',
	mixins: [dashboardNewsMessageArguments],
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
		};
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
			return this.isEdit ? 'put' : 'post';
		},
		isEdit() {
			return this.id !== 'new';
		},
	},
	methods: {
		onSubmitSucess(data) {
			if (!this.isEdit) {
				this.$router.push({ name: 'dashboard-news-edit', params: { id: data.id } });
			}
		},
		onChange({formData}) {
			this.formData = formData;
		},
		escapeArgumentKey(key) {
			return `{{${key}}}`;
		},
	},
};
</script>
