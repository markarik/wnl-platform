<template>
	<wnl-slides-editor
		:slide-id="Number(slideId) || 0"
		:screen-id="Number(screenId) || 0"
		:resource-url="resourceUrl"
		:excluded="['snippet']"
		remove
		@resetSearchInputs="resetSearchInputs"
	>
		<wnl-slides-search
			@screenIdChange="saveScreenId"
			@slideIdChange="saveSlideId"
			@resourceUrlFetched="onResourceUrlFetched"
			:slide-id="Number(slideId) || 0"
			:screen-id="Number(screenId) || 0"
		/>
	</wnl-slides-editor>
</template>

<script>
import SlidesEditor from 'js/admin/components/slides/SlideEditor';
import SlidesSearch from 'js/admin/components/slides/SlidesSearch';
import {getApiUrl} from 'js/utils/env';

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
		};
	},
	methods: {
		resetSearchInputs() {
			this.slideId = 0;
			this.screenId = 0;
		},
		saveScreenId(event) {
			this.screenId = event.target.value;
		},
		saveSlideId(event) {
			this.slideId = event.target.value;
		},
		onResourceUrlFetched({url, slideId}) {
			this.slideId = slideId;
			this.resourceUrl = url;
		}
	},
	mounted() {
		const slideId = this.$route.query.slideId;
		this.screenId = this.$route.query.screenId;
		this.slideId = slideId;
		if (slideId) {
			this.onResourceUrlFetched({
				url: getApiUrl(`slides/${slideId}`),
				slideId: slideId,
			});
		}
	}
};
</script>
