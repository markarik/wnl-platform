<template>
	<div class="screens-editor">
		<wnl-alert v-for="(alert, timestamp) in alerts"
			:alert="alert"
			cssClass="fixed"
			:key="timestamp"
			:timestamp="timestamp"
			@delete="onDelete"
		></wnl-alert>
		<div class="screens-list">
			<p class="title is-5">Ekrany</p>
			<wnl-screens-list :screens="screens" ref="ScreensList"></wnl-screens-list>
		</div>
		<div class="screen-editor" v-if="loaded">
			<form>
				<!-- Screen meta -->
				<div class="field is-grouped">
					<div class="control">
						<wnl-form-input :form="screenForm" name="name" v-model="screenForm.name"></wnl-form-input>
					</div>
					<div class="control">
						<a class="button is-success is-small"
							:class="{'is-loading': loading}"
							:disabled="!hasChanged"
							@click="onSubmit">
							<span class="margin right">Zapisz</span>
							<span class="icon is-small">
								<i class="fa fa-save"></i>
							</span>
						</a>
					</div>
				</div>

				<!-- Screen type -->
				<div class="field is-grouped">
					<div class="control">
						<label class="label">Typ ekranu</label>
						<span class="select">
							<wnl-select
								:form="screenForm"
								:options="typesOptions"
								name="type"
								v-model="screenForm.type"
							>
							</wnl-select>
						</span>
					</div>
					<div class="control" v-if="currentType && currentType.hasMeta">
						<label class="label">{{currentType.metaTitle}}</label>
						<span class="select">
							<wnl-select
								:form="screenForm"
								:options="getMetaResourcesList(screenForm.type)"
								name="meta"
								v-model="screenForm.meta"
							></wnl-select>
							TODO
							<wnl-form-textarea
								:form="screenForm"
								name="meta"
								v-model="screenForm.meta"
							/>
						</span>
					</div>
				</div>

				<!-- Screen content -->
				<div class="screen-content-editor">
					<quill
						:form="screenForm"
						name="content"
						v-model="screenForm.content">
					</quill>
				</div>
			</form>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.field.is-grouped
		align-items: center

	.screens-editor
		border-top: $border-light-gray
		display: flex
		margin-top: $margin-big
		padding-top: $margin-big

		.title.is-5
			border-bottom: $border-light-gray
			padding-bottom: $margin-base

	.screens-list
		border-right: $border-light-gray
		flex: 0 0 auto
		padding: $margin-base
		min-width: 250px

	.screen-editor
		flex: 8 auto
		padding: 0 $margin-base $margin-base

	.screen-content-editor
		margin-top: $margin-big

		.ql-container
			max-height: 50vh
</style>

<script>
	import _ from 'lodash'
	import { set } from 'vue'

	import ScreensList from 'js/admin/components/lessons/edit/ScreensList.vue'
	import Form from 'js/classes/forms/Form'
	import Input from 'js/admin/components/forms/Input.vue'
	import Quill from 'js/admin/components/forms/Quill.vue'
	import Select from 'js/admin/components/forms/Select.vue'

	import { alerts } from 'js/mixins/alerts'
	import { getApiUrl } from 'js/utils/env'
	import WnlFormTextarea from "js/admin/components/forms/Textarea";

	let types = {
		html: {
			text: 'Tekst',
			value: 'html',
			hasMeta: false,
		},
		quiz: {
			text: 'Zestaw pytań',
			value: 'quiz',
			hasMeta: true,
			metaTitle: 'Wybierz zestaw pytań',
			metaResource: 'quizes',
		},
		end: {
			text: 'Zakończenie',
			value: 'end',
			hasMeta: false,
		},
		mockexam: {
			text: 'Próbny egzamin',
			value: 'mockexam',
			hasMeta: false,
		},
		flashcards: {
			text: 'Powtórki',
			value: 'flashcards',
			metaTitle: 'Wybierz zestaw powtórek',
			hasMeta: true,
		},
	}

	export default {
		name: 'ScreensEditor',
		components: {
			WnlFormTextarea,
			'quill': Quill,
			'wnl-form-input': Input,
			'wnl-screens-list': ScreensList,
			'wnl-select': Select,
		},
		mixins: [ alerts ],
		data() {
			return {
				ready: false,
				loading: false,
				screenForm: new Form({
					content: null,
					meta: null,
					name: null,
					type: null,
				}),
				screens: [],
				quiz_sets: [],
			}
		},
		computed: {
			screenId() {
				return this.$route.params.screenId
			},
			loaded() {
				return !!this.$route.params.screenId
			},
			selectedTypeData() {
				return this.types[this.selectedType]
			},
			screenFormResourceUrl() {
				return getApiUrl(`screens/${this.$route.params.screenId}`)
			},
			typesOptions() {
				return Object.keys(types).map((key, index) => types[key])
			},
			currentType() {
				let type = this.screenForm.type
				if (type !== null && types.hasOwnProperty(type)) {
					return types[type]
				}
 			},
			hasChanged() {
				return !_.isEqual(this.screenForm.data(), this.screenForm.originalData)
			},
		},
		methods: {
			getMetaResourcesList(type) {
				return this.quiz_sets
			},
			formScreenMeta(resource, id) {
				let meta = {
					resources: [
						{
							id: id,
							name: resource
						}
					]
				}

				return JSON.stringify(meta)
			},
			fetchQuizSets() {
				return axios.get(getApiUrl('quiz_sets/all'))
					.then((response) => {
						_.forEach(response.data, (quiz) => {
							this.quiz_sets.push({
								text: quiz.name,
								value: this.formScreenMeta('quiz_sets', quiz.id),
							})
						})
					})
			},
			populateScreenForm() {
				axios.get(this.screenFormResourceUrl)
					.then(response => {
						Object.keys(response.data).forEach((field) => {
							let value = response.data[field]
							if (_.isObject(value)) {
								value = JSON.stringify(value)
							}
							this.screenForm[field] = value
							this.screenForm.originalData[field] = value
						})
					})
			},
			onSubmit() {
				if (!this.hasChanged) {
					return false
				}

				this.loading = true

				if (this.currentType.hasMeta) {
					this.screenForm.meta = unescape(this.screenForm.meta)
				} else {
					this.screenForm.meta = '{}'
				}

				this.screenForm.put(this.screenFormResourceUrl)
					.then(response => {
						this.loading = false
						this.screenForm.originalData = this.screenForm.data()
						this.successFading('Zapisano!', 2000)
						return this.$refs.ScreensList.fetchScreens()
					})
					.catch(exception => {
						this.loading = false
						this.errorFading('Nie wyszło :(', 2000)
						$wnl.logger.capture(exception)
					})
			}
		},
		mounted() {
			this.fetchQuizSets()
			if (this.screenId) {
				this.populateScreenForm()
			}
		},
		watch: {
			'$route': 'populateScreenForm'
		}
	}
</script>
