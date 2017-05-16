<template>
	<div class="lesson-editor">
		<form>
			<div class="field is-grouped">
				<div class="control">
					<span class="select">
						<!-- <select>
							<option v-for="group in groups" :value="group.id">
								{{group.name}}
							</option>
						</select> -->
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
				<div class="control">
					<a class="button is-success" :disabled="!hasChanged" @click="lessonFormSubmit">
						Zapisz
					</a>
				</div>
			</div>
		</form>
		<wnl-screens-editor></wnl-screens-editor>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import _ from 'lodash'

	import Form from 'js/classes/forms/Form'
	import { getApiUrl } from 'js/utils/env'

	import ScreensEditor from 'js/admin/components/lessons/edit/ScreensEditor.vue'
	import Input from 'js/components/global/form/Input.vue'
	import Select from 'js/components/global/form/Select.vue'

	export default {
		name: 'LessonEditor',
		components: {
			'wnl-screens-editor': ScreensEditor,
			'wnl-input': Input,
			'wnl-select': Select,
		},
		data() {
			return {
				form: new Form({
					group_id: null,
					groups: null,
					name: null,
				}),
				groups: [],
			}
		},
		computed: {
			lessonResourceUrl() {
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
				this.form.group_id = this.form.groups
				this.form.put(this.lessonResourceUrl)
					.then(response => console.log('Yoopi!'))
					.catch(exception => {
						this.submissionFailed = true
						$wnl.logger.capture(exception)
					})
			}
		},
		mounted() {
			this.fetchGroups()
				.then(() => {
					this.form.populate(this.lessonResourceUrl)
				})
		}
	}
</script>
