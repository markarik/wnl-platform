<template>
	<div class="screens-editor">
		<div class="screens-list">
			<p class="title is-5">Ekrany</p>
			<wnl-screens-list-item v-for="screen in screens"
				:name="screen.name"
				:id="screen.id">
			</wnl-screens-list-item>
			<div class="screens-list-add">
				<a><span class="icon is-small"><i class="fa fa-plus"></i></span> Dodaj ekran</a>
			</div>
		</div>
		<div class="screen-editor" v-if="loaded">

			<form @submit.prevent="onScreenFormSubmit">
				<!-- Screen meta -->
				<div class="field is-grouped">
					<div class="control">
						<wnl-form-input :form="screenForm" name="name" v-model="screenForm.name"></wnl-form-input>
					</div>
					<div class="control">
						<a class="button is-success" @click="onSubmit">Zapisz</a>
					</div>
				</div>

				<!-- Screen type -->
				<div class="field is-grouped">
					<div class="control">
						<label class="label">Typ ekranu</label>
						<span class="select">
							<select v-model="selectedType">
								<option v-for="(meta, type) in types" :value="type">
									{{type}}
								</option>
							</select>
						</span>
					</div>
					<div class="control" v-if="selectedTypeData.hasMeta">
						<label class="label">{{selectedTypeData.metaTitle}}</label>
					</div>
				</div>

				<!-- Screen content -->
				<div class="screen-content-editor">
					<quill :options="{ theme: 'snow' }"
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

	.screens-list-add
		margin-top: $margin-big
		text-align: center

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

	import Form from 'js/classes/forms/Form'
	import ScreensListItem from 'js/admin/components/lessons/edit/ScreensListItem.vue'
	import Input from 'js/components/global/form/Input.vue'
	import Quill from 'js/components/global/form/Quill.vue'

	import { getApiUrl } from 'js/utils/env'

	export default {
		name: 'ScreensEditor',
		components: {
			'quill': Quill,
			'wnl-form-input': Input,
			'wnl-screens-list-item': ScreensListItem,
		},
		data() {
			return {
				ready: false,
				screenForm: new Form({
					content: null,
					name: null,
					type: 'html',
				}),
				selectedType: 'quiz',
				types: {
					html: {
						hasMeta: false,
					},
					quiz: {
						hasMeta: true,
						metaTitle: 'Zestaw pytań',
						metaResource: 'quizes',
					},
					slideshow: {
						hasMeta: true,
						metaTitle: 'Prezentacja',
						metaResource: 'slideshow',
					},
					end: {
						hasMeta: false,
					},
				},
				screens: [],
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
			screensListApiUrl() {
				return getApiUrl(`screens/search?q=lesson_id:${this.$route.params.lessonId}`)
			}
		},
		methods: {
			fetchScreens() {
				return axios.get(this.screensListApiUrl)
					.then((response) => {
						this.screens = response.data
					})
			},
			populateScreenForm() {
				this.screenForm.populate(this.screenFormResourceUrl)
			},
			onSubmit() {
				this.screenForm.put(this.screenFormResourceUrl)
					.then(response => console.log('Yoopi!'))
					.catch(exception => {
						this.submissionFailed = true
						$wnl.logger.capture(exception)
					})
			}
		},
		mounted() {
			this.fetchScreens()

			if (this.screenId) {
				this.populateScreenForm()
			}
		},
		watch: {
			'$route': 'populateScreenForm'
		}
	}
</script>
