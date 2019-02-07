<template>
	<wnl-content-item-classifier-editor
		class="margin top"
		:content-item-id="currentSlideId"
		:content-item-type="CONTENT_TYPES.SLIDE"
	/>
</template>

<script>
import {mapActions} from 'vuex';
import WnlContentItemClassifierEditor from 'js/components/global/contentClassifier/ContentItemClassifierEditor';
import {CONTENT_TYPES} from 'js/consts/contentClassifier';

export default {
	components: {
		WnlContentItemClassifierEditor
	},
	props: {
		currentSlideId: {
			type: Number,
			required: true,
		},
	},
	data() {
		return {
			CONTENT_TYPES
		};
	},
	methods: {
		...mapActions('contentClassifier', ['fetchTaxonomyTerms']),
		loadTerms: _.debounce(async function (slideId) {
			this.fetchTaxonomyTerms({contentType: CONTENT_TYPES.SLIDE, contentIds: [slideId]});
		}, 300, {leading: false, trailing: true}),
	},
	watch: {
		currentSlideId(currentSlideId) {
			this.loadTerms(currentSlideId);
		}
	},
	mounted() {
		if (this.currentSlideId) {
			this.loadTerms(this.currentSlideId);
		}
	}
};
</script>
