<template>
	<div>
		<wnl-slides-editor title="Dodaj slajd" method="post" :resourceUrl="resourceUrl" :requestPayload="requestPayload" @contentChanged="onContentChanged">
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
			</div>

			<a slot="action" class="button" :disabled="!this.slide && !this.screenId" @click="preview">Podgląd</a>
		</wnl-slides-editor>
		<div ref="preview-modal" class="modal" :class="{'is-active': showModal}">
			<div class="modal-background" @click="showModal = false"></div>
			<div class="modal-content">
				<iframe :srcdoc="content"/>
			</div>
			<button class="modal-close is-large" aria-label="close" @click="showModal = false"></button>
		</div>

	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	.modal-content
		width: 90vw
		height: 90vh

		iframe
			width: 100%
			height: 100%
</style>


<script>
import SlidesEditor from 'js/admin/components/slides/SlidesEditor'
import { getApiUrl } from 'js/utils/env'

export default {
	name: 'AddSlide',
	components: {
		'wnl-slides-editor': SlidesEditor,
	},
	data() {
		return {
			screenId: null,
			slideOrderNo: null,
			slide: '',
			resourceUrl: getApiUrl('slides'),
			showModal: false,
			content: ''
		}
	},
	computed: {
		requestPayload() {
			return {
				screen: this.screenId,
				order_number: this.slideOrderNo
			}
		}
	},
	methods: {
		onContentChanged(content) {
			this.slide = content;
		},
		preview(event) {
			event.preventDefault();
			event.stopPropagation();

			this.content = ''
			this.showModal = true

			axios.post(getApiUrl(`slideshow_builder/screen/${this.screenId}/preview`), {
				slide: this.slide
			}).then(({ data }) => {
				this.content = data
			})
		}
	}
}
</script>
