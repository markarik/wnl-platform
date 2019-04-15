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
							<input
								type="text" class="input"
								@keyup.enter="getSlide"
								:value="screenId" @input="(event) => $emit('screenIdChange', event)"
							>
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
							<input
								type="text" class="input"
								@keyup.enter="getSlide"
								:value="slideId" @input="(event) => $emit('slideIdChange', event)">
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
import axios from 'axios';
import { resource } from 'js/utils/config';
import { getApiUrl } from 'js/utils/env';

export default {
	name: 'SlidesSearch',
	data() {
		return {
			slideOrderNo: null,
			loading: false,
			resourceUrl: '',
			error: false
		};
	},
	props: {
		slideId: {
			type: Number,
		},
		screenId: {
			tyoe: Number,
		}
	},
	computed: {
		slideNumber() {
			return String(this.slideOrderNo - 1);
		}
	},
	methods: {
		getSlide() {
			this.loading = true;

			if (!this.slideId) {
				this.getSlideshowId()
					.then(slideshowId => {
						return this.getSlideId(slideshowId);
					})
					.then(slideId => {
						this.resourceUrl = getApiUrl(`slides/${slideId}`);
						this.loading = false;
						this.slideId = slideId;

						this.$emit('resourceUrlFetched', {
							url: this.resourceUrl,
							slideId: this.slideId,
							screenId: this.screenId
						});
						this.error = false;
					})
					.catch(exception => {
						$wnl.logger.error(exception);
						this.loading = false;
						this.error = true;
					});
			} else {
				this.resourceUrl = getApiUrl(`slides/${this.slideId}`);
				this.slideOrderNo = null;
				this.loading = false;

				this.$emit('resourceUrlFetched', {
					url: this.resourceUrl,
					slideId: this.slideId
				});
			}
		},
		getSlideshowId() {
			return axios.get(getApiUrl(`screens/${this.screenId}`))
				.then(response => {
					let resources    = response.data.meta.resources;
					let resourceName = resource('slideshows');
					let slideshowId;
					Object.keys(resources).forEach((key) => {
						if (resources[key].name === resourceName) {
							slideshowId = resources[key].id;
						}
					});
					return slideshowId;
				});
		},
		getSlideId (slideshowId) {
			return axios.post(getApiUrl('presentables/slides/byOrderNumber'), {
				presentable_type: 'App\\Models\\Slideshow',
				presentable_id: slideshowId,
				order_number: this.slideNumber
			})
				.then(response => {
					return response.data[0].slide_id;
				});
		},
	},
};
</script>
