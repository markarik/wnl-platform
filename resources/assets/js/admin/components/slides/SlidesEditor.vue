<template>
	<div class="slides-editor">
		<wnl-alert v-for="(alert, timestamp) in alerts"
				   :alert="alert"
				   cssClass="fixed"
				   :key="timestamp"
				   :timestamp="timestamp"
				   @delete="onDelete"
		></wnl-alert>
		<p class="title is-3">Edycja slajdu</p>
		<div class="slides-search">
			<div class="level margin vertical">
				<div class="level-left">
					<div class="level-item">
						<div class="field is-grouped">
							<div class="control">
								<label class="label">Numer screena</label>
								<input @keyup.enter="getSlide" type="text" class="input" v-model="screenId">
							</div>
							<div class="control">
								<label class="label">Numer slajdu</label>
								<input @keyup.enter="getSlide" type="text" class="input" v-model="slideOrderNo">
							</div>
						</div>
					</div>
					<div class="level-item">
						<div class="field is-grouped">
							<div class="control">
								<label class="label">lub ID slajdu</label>
								<input @keyup.enter="getSlide" type="text" class="input" v-model="slideIdInput">
							</div>
						</div>
					</div>
				</div>
				<div class="level-right">
					<div class="level-item">
						<a class="button is-outlined" @click="getSlide" :class="{'is-loading': loadingSlide}">
							Zaciung slajd
						</a>
					</div>
				</div>
			</div>
		</div>

		<div class="notification is-success has-text-centered" v-if="saved">
			Zapisano
		</div>
		<div class="notification is-danger has-text-centered" v-if="submissionFailed">
			Coś poszło nie tak...
		</div>

		<form class="" action="" method="POST" @submit.prevent="onSubmit"
			  @keydown="form.errors.clear($event.target.name)">

			<div class="slide-content-editor">
				<wnl-form-code type="text" name="content" :form="form" v-model="form.content"></wnl-form-code>
			</div>
			<div class="level">
				<div class="level-left">
					<div class="level-item">
						<p class="control">
							<wnl-form-checkbox type="text" name="is_functional" :form="form"
											   v-model="form.is_functional">
								Slajd funkcjonalny?
							</wnl-form-checkbox>
						</p>
					</div>
				</div>
				<div class="level-right">
					<div class="level-item">
						<a class="button is-primary" :class="{'is-loading': updatingChart}" v-if="chartReady" @click="updateChart">Aktualizuj diagram</a>
					</div>
					<div class="level-item">
						<a class="button is-primary" :class="{'is-loading': loading}" :disabled="form.errors.any()" @click="onSubmit">Zapisz slajd</a>
					</div>
				</div>
			</div>
		</form>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.slides-editor
		width: 100%

	.slide-content-editor
		border: $border-light-gray
		height: 500px
		margin: $margin-big 0
</style>

<script>
	import Form from 'js/classes/forms/Form'
	import Code from 'js/admin/components/forms/Code'
	import Checkbox from 'js/admin/components/forms/Checkbox'
	import {resource} from 'js/utils/config'
	import {getUrl} from 'js/utils/env'
	import _ from 'lodash'
	import { alerts } from 'js/mixins/alerts'

	const SECTION_OPEN_TAG_REGEX = /<section.*>$/
	const SECTION_CLOSE_TAG_REGEX = /<\/section>$/

	export default {
		name: 'SlideEditor',
		components: {
			'wnl-form-code': Code,
			'wnl-form-checkbox': Checkbox,
		},
		mixins: [ alerts ],
		data() {
			return {
				form: new Form({
					content: null,
					is_functional: null,
				}),
				resourceUrl: '',
				saved: false,
				submissionFailed: false,
				slideOrderNo: '',
				screenId: '',
				loadingSlide: false,
				loading: false,
				slideId: null,
				slideIdInput: '',
				updatingChart: false,
			}
		},
		computed: {
			slideNumber() {
				return this.slideOrderNo - 1
			},
			chartReady() {
				let match = null
				if (this.form.content){
					match = this.form.content.match('class="chart"')
				}
				return !!this.slideId && !!match
			}
		},
		methods: {
			onSubmit() {
				this.loading = true
				this.reset()

				const isValid = this.validateContent(this.form.content)
				if (!isValid) {
					this.errorFading('Upewnij się że slajd posiada tag section na początku i na końcu treści')
					this.loading = false;
					this.submissionFailed = true
					return;
				}
				this.form.put(this.resourceUrl)
						.then(response => {
							this.saved = true
							this.loading = false
						})
						.catch(exception => {
							this.submissionFailed = true
							this.loading = false
						})
			},
			validateContent(content) {
				const contentSplited = content
					.split('\n')
					.map(line => line.trim())
					.filter(line => line);

				return SECTION_OPEN_TAG_REGEX.test(contentSplited[0])
					&& SECTION_CLOSE_TAG_REGEX.test(contentSplited[contentSplited.length - 1])
			},
			getSlide() {
				let exclude = ['snippet']

				this.loadingSlide = true
				this.reset()
				if (!this.slideIdInput) {
					this.getSlideshowId()
						.then(slideshowId => {
							return this.getSlideId(slideshowId)
						})
						.then(slideId => {
							this.resourceUrl = `/papi/v1/slides/${slideId}`
							this.form.populate(this.resourceUrl, exclude)
							this.loadingSlide = false
							this.slideId = slideId
						})
						.catch(exception => {
							this.submissionFailed = true
							this.loadingSlide = false
							console.log(exception)
						})
				} else {
					this.resourceUrl = `/papi/v1/slides/${this.slideIdInput}`
					this.form.populate(this.resourceUrl, exclude)
					this.loadingSlide = false
					this.slideId = parseInt(this.slideIdInput)
				}
			},
			getSlideshowId() {
				return axios.get(`/papi/v1/screens/${this.screenId}`)
						.then(response => {
							let resources    = response.data.meta.resources
							let resourceName = resource('slideshows')
							let slideshowId
							Object.keys(resources).forEach((key) => {
								if (resources[key].name === resourceName) {
									slideshowId = resources[key].id
								}
							})
							return slideshowId
						})
			},
			getSlideId (slideshowId) {
				const conditions = {
					query: {
						where: [
							['presentable_type', '=', 'App\\Models\\Slideshow'],
							['presentable_id', '=', slideshowId],
							['order_number', '=', this.slideNumber],
						]
					}
				}
				return axios.post('/papi/v1/presentables/.search', conditions)
						.then(response => {
							return response.data[0].slide_id
						})
			},
			reset() {
				this.saved            = false
				this.submissionFailed = false
			},
			updateChart() {
				this.updatingChart = true
				axios.get(getUrl(`admin/update-charts/${this.slideId}`))
						.then(response => {
							this.getSlide()
							this.successFading('Diagram zaktualizowany!', 2000)
							this.updatingChart = false
						})
						.catch(error => {
							this.errorFading('Ups... Coś poszło nie tak.', 4000)
							$wnl.logger.capture(error)
							this.updatingChart = false
						})
			}
		}
	}
</script>
