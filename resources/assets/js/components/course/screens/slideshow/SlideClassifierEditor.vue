<template>
	<wnl-content-item-classifier-editor
		class="margin top"
		:content-item-id="currentSlideId"
		:content-item-type="CONTENT_TYPES.SLIDE"
		:is-active="isActive"
		:is-focused="isFocused"
		@blur="onBlur"
		@updateIsActive="onUpdateIsActive"
		@editorCreated="onEditorCreated"
		@editorDestroyed="onEditorDestroyed"
	/>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import { debounce } from 'lodash';

import WnlContentItemClassifierEditor from 'js/components/global/contentClassifier/ContentItemClassifierEditor';

import { CONTENT_TYPES } from 'js/consts/contentClassifier';

export default {
	components: {
		WnlContentItemClassifierEditor,
	},
	props: {
		currentSlideId: {
			type: Number,
			required: true,
		},
	},
	data() {
		return {
			activateWithShortcutKeyId: 'slideClassfier',
			CONTENT_TYPES,
		};
	},
	computed: {
		...mapGetters('activateWithShortcutKey', ['isActiveByUid', 'isFocusedByUid']),
		isActive() {
			return this.isActiveByUid(this.activateWithShortcutKeyId);
		},
		isFocused() {
			return this.isFocusedByUid(this.activateWithShortcutKeyId);
		},
	},
	methods: {
		...mapActions('contentClassifier', ['fetchTaxonomyTerms']),
		...mapActions('activateWithShortcutKey', ['setActiveInstance', 'resetActiveInstance', 'register', 'deregister', 'resetFocus']),
		loadTerms: debounce(async function (slideId) {
			this.fetchTaxonomyTerms({ contentType: CONTENT_TYPES.SLIDE, contentIds: [slideId] });
		}, 300, { leading: false, trailing: true }),
		onUpdateIsActive(isActive) {
			if (isActive) {
				this.setActiveInstance(this.activateWithShortcutKeyId);
			} else {
				this.resetActiveInstance();
			}
		},
		onBlur() {
			this.resetFocus();
		},
		onEditorCreated() {
			this.register(this.activateWithShortcutKeyId);
		},
		onEditorDestroyed() {
			this.deregister(this.activateWithShortcutKeyId);
		},
	},
	watch: {
		currentSlideId(currentSlideId) {
			this.loadTerms(currentSlideId);
		},
	},
	mounted() {
		if (this.currentSlideId) {
			this.loadTerms(this.currentSlideId);
		}
	},
};
</script>
