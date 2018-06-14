<template>
	<wnl-slides-editor
		:slideId="Number(slideId)"
		:screenIdProps="Number(screenId)"
		:resourceUrl="resourceUrl"
		:excluded="['snippet']"
		:remove="true"
		@resetSearchInputs="resetSearchInputs"
	>
		<wnl-slides-search
			@emitScreenId="saveScreenId"
			@emitSlideId="saveSlideId"
			@resourceUrlFetched="onResourceUrlFetched"
			:slideId="Number(slideId)"
			:screenId="Number(screenId)"
		/>
	</wnl-slides-editor>
</template>

<script>
	import SlidesEditor from 'js/admin/components/slides/SlideEditor'
	import SlidesSearch from 'js/admin/components/slides/SlidesSearch'

	export default {
		name: 'EditSlide',
		components: {
			'wnl-slides-editor': SlidesEditor,
			'wnl-slides-search': SlidesSearch,
		},
		data() {
			return {
				slideId: 0,
				screenId: 0,
				resourceUrl: ''
			}
		},
		methods: {
			resetSearchInputs() {
				this.slideId = 0
				this.screenId = 0
			},
			saveScreenId(newVal) {
				this.screenId = newVal
			},
			saveSlideId(newVal) {
				this.slideId = newVal
			},
			onResourceUrlFetched({url, slideId, screenId}) {
				this.slideId = slideId;
				this.resourceUrl = url;
				this.screenId = screenId;
			}
		},
		mounted() {
			const slideId = this.$route.query.slideId
			const screenId = this.$route.query.screenId
			this.screenId = screenId
			this.slideId = slideId
			if (slideId && screenId) {
				this.onResourceUrlFetched({
					url: `/papi/v1/slides/${slideId}`,
					slideId: slideId,
					screenId: screenId
				})
			}
		}
	}
</script>
