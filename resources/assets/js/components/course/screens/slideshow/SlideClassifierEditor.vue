<template>
	<wnl-activate-with-shortcut-key>
		<template slot-scope="activateWithShortcutKey">
			<wnl-content-item-classifier-editor
				class="margin top"
				:is-active="activateWithShortcutKey.isActive"
				:content-item-id="currentSlideId"
				:content-item-type="CONTENT_TYPES.SLIDE"
			/>
		</template>
	</wnl-activate-with-shortcut-key>
</template>

<script>
import {mapActions} from 'vuex';

import WnlContentItemClassifierEditor from 'js/components/global/contentClassifier/ContentItemClassifierEditor';
import WnlActivateWithShortcutKey from 'js/components/global/ActivateWithShortcutKey';

import {CONTENT_TYPES} from 'js/consts/contentClassifier';

export default {
	components: {
		WnlContentItemClassifierEditor,
		WnlActivateWithShortcutKey,
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
