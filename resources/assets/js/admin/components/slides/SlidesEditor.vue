<template>
	<div class="slides-editor">
		<p class="title is-3">Edycja slajdu</p>
		<div class="slides-search">
			<div class="level margin vertical">
				<div class="level-left">
					<div class="level-item">
						<div class="field is-grouped">
							<div class="control">
								<label class="label">Numer screena</label>
								<input type="text" class="input" v-model="screenId">
							</div>
							<div class="control">
								<label class="label">Numer slajdu</label>
								<input type="text" class="input" v-model="slideOrderNo">
							</div>
						</div>
					</div>
				</div>
				<div class="level-right">
					<div class="level-item">
						<a class="button is-outlined" @click="getSlide">Zaciung slajd</a>
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
						<a class="button is-primary" :disabled="form.errors.any()" @click="onSubmit">Zapisz slajd</a>
					</div>
				</div>
			</div>
		</form>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.slides-editor
		max-width: 800px

	.slide-content-editor
		border: $border-light-gray
		height: 500px
		margin: $margin-big 0
</style>

<script>
	import Form from 'js/classes/forms/Form'
	import Code from 'js/components/global/form/Code'
	import Checkbox from 'js/components/global/form/Checkbox'
	import _ from 'lodash'
	import {resource} from 'js/utils/config'

	export default {
		name: 'SlideEditor',
		components: {
			'wnl-form-code': Code,
			'wnl-form-checkbox': Checkbox,
		},
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
			}
		},
		methods: {
			onSubmit() {
				this.reset()
				this.form.put(this.resourceUrl)
						.then(response => this.saved = true)
						.catch(exception => {
							this.submissionFailed = true
						})
			},
			getSlide() {
				this.reset()
				this.getSlideshowId()
						.then(slideshowId => {
							return this.getSlideId(slideshowId)
						})
						.then(slideId => {
							this.resourceUrl = `/papi/v1/slides/${slideId}`
							this.form.populate(this.resourceUrl)
						})
						.catch(exception => {
							this.submissionFailed = true
							console.log(exception)
						})
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
				return axios.get(`/papi/v1/presentables/.search?q=
					order_number:${this.slideOrderNo},
					presentable_type:App%5CModels%5CSlideshow,
					presentable_id:${slideshowId}`)
						.then(response => {
							return response.data[0].slide_id
						})
			},
			reset() {
				this.saved            = false
				this.submissionFailed = false
			}
		}
	}
</script>
