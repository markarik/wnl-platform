<template>
	<div class="slides-search">
		<div class="notification is-danger has-text-centered" v-show="error">
			Nie udało się znaleźć slajdu dla podanych argumentów
		</div>

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
					<a class="button is-outlined" @click="getSlide" :class="{'is-loading': loading}">
						Zaciung slajd
					</a>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import {resource} from 'js/utils/config'

export default {
	name: 'SlidesSearch',
	data() {
		return {
			screenId: null,
			slideOrderNo: null,
			slideIdInput: null,
			loading: false,
			resourceUrl: '',
			error: false
		}
	},
	computed: {
		slideNumber() {
			return String(this.slideOrderNo - 1)
		}
	},
	methods: {
		getSlide() {
				this.loading = true

				if (!this.slideIdInput) {
					this.getSlideshowId()
						.then(slideshowId => {
							return this.getSlideId(slideshowId)
						})
						.then(slideId => {
							this.resourceUrl = `/papi/v1/slides/${slideId}`
							this.loading = false
							this.slideId = slideId

							this.$emit('resourceUrlFetched', {
								url: this.resourceUrl,
								slideId: this.slideId,
								screenId: this.screenId
							})
							this.error = false
						})
						.catch(exception => {
							console.error(exception)
							this.loading = false
							this.error = true
						})
				} else {
					this.resourceUrl = `/papi/v1/slides/${this.slideIdInput}`
					this.slideId = parseInt(this.slideIdInput)
					this.screenId = null
					this.slideOrderNo = null
					this.loading = false

					this.$emit('resourceUrlFetched', {
						url: this.resourceUrl,
						slideId: this.slideId
					})
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
	},
	mounted() {
		const slideId = Object.keys(this.$route.query)[0]
		if (slideId) {
			this.slideIdInput = slideId
		}
	}
}
</script>
