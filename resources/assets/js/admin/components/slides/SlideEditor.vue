<template>
	<div class="slides-editor">
		<wnl-alert v-for="(alert, timestamp) in alerts"
			:alert="alert"
			cssClass="fixed"
			:key="timestamp"
			:timestamp="timestamp"
			@delete="onDelete"
		/>
		<p class="title is-3">{{title}}</p>

		<slot/>

		<div class="notification is-success has-text-centered" v-show="saved">
			Zapisano
		</div>
		<div class="notification is-danger has-text-centered" v-show="submissionFailed">
			Coś poszło nie tak...
		</div>

		<form class="" action="" method="POST" @submit.prevent="onSubmit"
			  @keydown="form.errors.clear($event.target.name)">

			<div class="slide-content-editor">
				<wnl-form-code type="text" name="content" :form="form" v-model="form.content"/>
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
					<slot name="action"/>
					<div class="level-item">
						<a class="button" :disabled="!this.slideId && !this.screenId" @click="preview">Podgląd</a>
					</div>
					<div class="level-item">
						<a class="button is-primary" :class="{'is-loading': loading}" :disabled="form.errors.any() || !form.content" @click="onSubmit">Zapisz slajd</a>
					</div>
				</div>
			</div>
		</form>
		<wnl-slide-preview :showModal="showPreviewModal" :content="previewModalContent" @closeModal="showPreviewModal=false"/>
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
	import _ from 'lodash'

	import Form from 'js/classes/forms/Form'
	import {getUrl, getApiUrl} from 'js/utils/env'
	import Code from 'js/admin/components/forms/Code'
	import SlidePreview from 'js/admin/components/slides/SlidePreview'
	import Checkbox from 'js/admin/components/forms/Checkbox'
	import { alerts } from 'js/mixins/alerts'

	const SECTION_OPEN_TAG_REGEX = /<section.*>$/
	const SECTION_CLOSE_TAG_REGEX = /<\/section>$/
	const COURSE_TAG_REGEX = /[#!]+\(.*\)/
	const FUNCTIONAL_SLIDE_TAG_REGEX = /[#!]+\(functional\)/

	export default {
		name: 'SlideEditor',
		components: {
			'wnl-form-code': Code,
			'wnl-form-checkbox': Checkbox,
			'wnl-slide-preview': SlidePreview,
		},
		props: {
			title: {
				type: String,
				default: 'Edytuj slajd'
			},
			resourceUrl: {
				type: String,
				default: ''
			},
			excluded: {
				type: Array,
				default: () => []
			},
			method: {
				type: String,
				default: 'put'
			},
			requestPayload: {
				type: Object,
				default: () => {}
			},
			slideId: {
				type: Number,
				default: 0
			},
			screenId: {
				type: Number,
				default: 0,
				validator: (value) => !isNaN(value)
			}
		},
		mixins: [ alerts ],
		data() {
			return {
				form: new Form({
					content: null,
					is_functional: null,
				}),
				showPreviewModal: false,
				previewModalContent: '',
				saved: false,
				submissionFailed: false,
				loading: false,
				updatingChart: false,
			}
		},
		computed: {
			chartReady() {
				let match = null
				if (this.form.content){
					match = this.form.content.match('class="chart"')
				}
				return !!this.slideId && !!match
			},
			content() {
				return this.form.content
			}
		},
		methods: {
			onSubmit() {
				this.loading = true
				this.reset()

				const isValid = this.validateContent(this.content)
				if (!isValid) {
					this.errorFading('Upewnij się że slajd posiada tag section na początku i na końcu treści')
					this.loading = false;
					this.submissionFailed = true
					return;
				}
				this.form.submit(this.method, this.resourceUrl, this.requestPayload)
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
			},
			preview(event) {
				event.preventDefault();
				event.stopPropagation();

				this.showPreviewModal = true

				axios.post(getApiUrl('slideshow_builder/preview'), {
					content: this.form.content,
					slideId: this.slideId ? this.slideId : null,
					screenId: this.screenId ? this.screenId : null
				}).then(({ data }) => {
					this.previewModalContent = data
				})
			},
			removeCourseTags() {
				if (FUNCTIONAL_SLIDE_TAG_REGEX.test(this.form.content)) {
					this.form.is_functional = true
				}
				this.form.content = this.form.content.replace(COURSE_TAG_REGEX, '')
			}
		},
		watch: {
			resourceUrl(newValue, oldValue) {
				newValue !== '' && this.form.populate(this.resourceUrl, this.excluded)
			},
			content(newValue, oldValue) {
				this.removeCourseTags()
			}
		}
	}
</script>