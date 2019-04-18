<template>
	<wnl-slide-editor
		:slide-id="Number(slideId) || 0"
		:screen-id="Number(screenId) || 0"
		:resource-url="resourceUrl"
		:excluded="['snippet']"
		:remove="true"
		@resetSearchInputs="resetSearchInputs"
	>
		<wnl-slide-search
			slot="above-content"
			:slide-id="Number(slideId) || 0"
			:screen-id="Number(screenId) || 0"
			@screenIdChange="saveScreenId"
			@slideIdChange="saveSlideId"
			@resourceUrlFetched="onResourceUrlFetched"
		/>
		<wnl-content-item-classifier-editor
			v-if="slideId > 0"
			slot="below-content"
			class="margin bottom"
			:is-always-active="true"
			:content-item-id="slideId"
			:content-item-type="CONTENT_TYPES.SLIDE"
		/>
	</wnl-slide-editor>
</template>

<script>
import { mapActions } from 'vuex';

import WnlSlideEditor from 'js/admin/components/slides/SlideEditor';
import WnlSlideSearch from 'js/admin/components/slides/SlidesSearch';
import WnlContentItemClassifierEditor from 'js/components/global/contentClassifier/ContentItemClassifierEditor';

import { getApiUrl } from 'js/utils/env';
import { CONTENT_TYPES } from 'js/consts/contentClassifier';

export default {
	name: 'EditSlide',
	components: {
		WnlSlideEditor,
		WnlSlideSearch,
		WnlContentItemClassifierEditor
	},
	data() {
		return {
			slideId: 0,
			screenId: 0,
			resourceUrl: '',
			CONTENT_TYPES,
		};
	},
	watch: {
		async slideId(slideId) {
			if (slideId) {
				await this.setupCurrentUser();
				await this.fetchTaxonomyTerms({ contentType: CONTENT_TYPES.SLIDE, contentIds: [slideId] });
			}
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
	},
	methods: {
		...mapActions(['setupCurrentUser']),
		...mapActions('contentClassifier', ['fetchTaxonomyTerms']),
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
		onResourceUrlFetched({ url, slideId }) {
			this.slideId = slideId;
			this.resourceUrl = url;
		}
	},
};
</script>
