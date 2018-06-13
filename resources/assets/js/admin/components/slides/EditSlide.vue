<template>
	<wnl-slides-editor
		:slideId="Number(slideId)"
		:screenId="Number(screenId)"
		:resourceUrl="resourceUrl"
		:excluded="['snippet']"
		:remove="true"
	>
		<wnl-slides-search
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
			onResourceUrlFetched({url, slideId, screenId}) {
				this.slideId = slideId;
				this.resourceUrl = url;
				this.screenId = screenId
			}
		},
		mounted() {
			const slideId = this.$route.query.slideId
			const screenId = this.$route.query.screenId
			this.screenId = screenId
			this.slideId = slideId
			if (slideId && screenId) {
				this.onResourceUrlFetched({
					screenId: screenId,
					slideId: slideId,
					url: `/papi/v1/slides/${slideId}`
				})
			}
		}
	}
</script>
