<template>
	<div v-if="canAccess" class="margin top">
		<wnl-content-item-classifier-editor
			v-if="slideWithTerms"
			:contentItem="slideWithTerms"
			@taxonomyTermAttached="onTermAttached"
			@taxonomyTermDetached="onTermDetached"
		/>
		<wnl-text-loader v-else></wnl-text-loader>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
</style>

<script>
import {mapGetters} from 'vuex';
import WnlContentItemClassifierEditor from 'js/components/global/contentClassifier/ContentItemClassifierEditor';
import {parseTaxonomyTermsFromIncludes} from 'js/utils/contentClassifier';
import {getApiUrl} from 'js/utils/env';
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
			slideWithTerms: null,
		};
	},
	computed: {
		...mapGetters(['isAdmin', 'isModerator']),
		canAccess() {
			return this.isAdmin || this.isModerator;
		},
	},
	methods: {
		loadTerms: _.debounce(async function (slideId) {
			this.slideWithTerms = null;
			try {
				const {data} = await axios.get(getApiUrl(`slides/${slideId}?include=taxonomy_terms.tags,taxonomy_terms.taxonomies,taxonomy_terms.ancestors.tags`));
				const {included, ...slide} = data;
				this.slideWithTerms = {
					...slide,
					type: CONTENT_TYPES.SLIDE,
					taxonomyTerms: parseTaxonomyTermsFromIncludes(slide.taxonomy_terms, included),
				};
			} catch (error) {
				$wnl.logger.capture(error);
			}
		}, 300, {leading: false, trailing: true}),
		onTermAttached(term) {
			this.slideWithTerms.taxonomyTerms.push(term);
		},
		onTermDetached(term) {
			const termIndex = this.slideWithTerms.taxonomyTerms.findIndex(({id}) => id === term.id);
			this.slideWithTerms.taxonomyTerms.splice(termIndex, 1);
		}
	},
	watch: {
		currentSlideId(currentSlideId) {
			if (this.canAccess) {
				this.loadTerms(currentSlideId);
			}
		}
	},
	mounted() {
		if (this.currentSlideId && this.canAccess) {
			this.loadTerms(this.currentSlideId);
		}
	}
};
</script>
