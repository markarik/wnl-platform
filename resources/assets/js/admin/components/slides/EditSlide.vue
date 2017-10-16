<template>
	<wnl-slides-editor
		:slideId="slideId"
		:resourceUrl="resourceUrl"
		:excluded="['snippet']"
	>
		<wnl-slides-search
			@resourceUrlFetched="onResourceUrlFetched"
		/>
	</wnl-slides-editor>
</template>

<script>
	import SlidesEditor from 'js/admin/components/slides/SlidesEditor'
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
				resourceUrl: ''
			}
		},
		methods: {
			onResourceUrlFetched({url, slideId}) {
				this.slideId = slideId;
				this.resourceUrl = url;
			}
		}
	}
</script>
