<template>
	<div class="lesson-editor">
		<wnl-alert v-for="(alert, timestamp) in alerts"
			:alert="alert"
			cssClass="fixed"
			:key="timestamp"
			:timestamp="timestamp"
			@delete="onDelete"
		></wnl-alert>
		<form @submit.prevent="lessonFormSubmit">
			<div class="field is-grouped">
				<div class="control">
					<span class="select">
						<wnl-select :form="form"
							:options="groups"
							name="groups"
							v-model="form.groups"
						></wnl-select>
					</span>
				</div>
				<wnl-input :form="form"
					name="name"
					v-model="form.name"
				></wnl-input>
				<wnl-form-checkbox
					class="checkbox button"
					:form="form"
					name="is_required"
					v-model="form.is_required"
				>Lekcja obowiązkowa</wnl-form-checkbox>
				<div class="control">
					<a class="button is-small is-success"
						:class="{'is-loading': loading}"
						:disabled="!hasChanged"
						@click="lessonFormSubmit">
						<span class="margin right">Zapisz</span>
						<span class="icon is-small">
							<i class="fa fa-save"></i>
						</span>
					</a>
				</div>
			</div>
		</form>
		<wnl-screens-editor></wnl-screens-editor>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.checkbox
		margin-right: 10px
		display: flex
		justify-content: space-between

</style>

<script>
	import _ from 'lodash'

	import Form from 'js/classes/forms/Form'
	import { getApiUrl } from 'js/utils/env'
	import { alerts } from 'js/mixins/alerts'

	import ScreensEditor from 'js/admin/components/lessons/edit/ScreensEditor.vue'
	import Input from 'js/admin/components/forms/Input.vue'
	import Select from 'js/admin/components/forms/Select.vue'
	import Checkbox from 'js/admin/components/forms/Checkbox.vue'

	export default {
		name: 'LessonEditor',
		components: {
			'wnl-screens-editor': ScreensEditor,
			'wnl-input': Input,
			'wnl-select': Select,
			'wnl-form-checkbox': Checkbox,
		},
		mixins: [ alerts ],
		data() {
			return {
				form: new Form({
					group_id: null,
					groups: null,
					name: null,
					is_required: null,
				}),
				groups: [],
				loading: false,
			}
		},
		computed: {
			resourceUrl() {
				return getApiUrl(`lessons/${this.$route.params.lessonId}`)
			},
			hasChanged() {
				return !_.isEqual(this.form.originalData, this.form.data())
			}
		},
		methods: {
			fetchGroups() {
				return axios.get(getApiUrl('groups/all'))
					.then((response) => {
						_.forEach(response.data, (group) => {
							this.groups.push({
								text: group.name,
								value: group.id,
							})
						})
					})
			},
			lessonFormSubmit() {
				if (!this.hasChanged) {
					return false
				}

				this.loading = true
				this.form.group_id = this.form.groups
				this.form.put(this.resourceUrl)
					.then(response => {
						this.loading = false
						this.successFading('Lekcja zapisana!', 2000)
						this.form.originalData = this.form.data()
					})
					.catch(exception => {
						this.loading = false
						this.errorFading('Nie udało się :(', 2000)
						$wnl.logger.capture(exception)
					})
			}
		},
		mounted() {
			this.fetchGroups()
				.then(() => {
					this.form.populate(this.resourceUrl)
				})
		}
	}
</script>
