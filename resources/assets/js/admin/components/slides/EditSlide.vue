<template>
	<wnl-slides-editor
		:slideId="slideId"
		:screenId="Number(screenId)"
		:resourceUrl="resourceUrl"
		:excluded="['snippet']"
		:remove="true"
	>
		<wnl-slides-search
			@resourceUrlFetched="onResourceUrlFetched"
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
			const slideId = Object.keys(this.$route.query)[0]
			if (slideId) {
				this.onResourceUrlFetched({
					slideId: slideId,
					url: `/papi/v1/slides/${slideId}`
				})
			}
		}
	}
</script>
