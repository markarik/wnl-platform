<template>
	<wnl-content-item-classifier-editor
		class="margin top"
		:content-item-id="currentSlideId"
		:content-item-type="CONTENT_TYPES.SLIDE"
		:is-active="isActive"
		:is-focused="isFocused"
		@updateIsActive="onUpdateIsActive"
		@editorCreated="onEditorCreated"
		@editorDestroyed="onEditorDestroyed"
	/>
</template>

<script>
import {mapActions} from 'vuex';

import WnlContentItemClassifierEditor from 'js/components/global/contentClassifier/ContentItemClassifierEditor';

import {CONTENT_TYPES} from 'js/consts/contentClassifier';
import {scrollToElement} from 'js/utils/animations';

export default {
	components: {
		WnlContentItemClassifierEditor,
	},
	props: {
		currentSlideId: {
			type: Number,
			required: true,
		},
		slidesIds: {
			type: Array,
			required: true,
		},
	},
	data() {
		return {
			isActive: false,
			isFocused: false,
			userHasAccess: false,
			CONTENT_TYPES,
		};
	},
	methods: {
		...mapActions('contentClassifier', ['fetchTaxonomyTerms']),
		getUidForShortcutKey(id) {
			return `slide-classifier-editor--${id}`;
		},
		loadTerms: _.debounce(async function (slideId) {
			this.fetchTaxonomyTerms({contentType: CONTENT_TYPES.SLIDE, contentIds: [slideId]});
		}, 300, {leading: false, trailing: true}),
		async onActivate(uid) {
			const slideId = Number(uid.split('--')[1]);
			const index = this.slidesIds.indexOf(slideId);

			this.isActive = true;

			if (index > -1) {
				this.$emit('navigateToSlide', index + 1);
				// TODO replace hardcoded classname with something dynamic
				scrollToElement(document.getElementsByClassName('wnl-slideshow-container')[0], 0, 500);
			}
		},
		onDeactivate() {
			this.isFocused = false;
		},
		onFocus() {
			this.isFocused = true;
		},
		onUpdateIsActive(isActive) {
			if (isActive) {
				this.$shortcutKeySetActiveInstance(this.getUidForShortcutKey(this.currentSlideId));
				this.isActive = true;
			} else {
				this.$shortcutKeyResetActiveInstance();
			}
		},
		onEditorCreated() {
			this.userHasAccess = true;
			this.registerSlidesForShortcutKeys(this.slidesIds);
		},
		onEditorDestroyed() {
			this.slidesIds.forEach((id) => {
				this.$shortcutKeyDeregister(this.getUidForShortcutKey(id));
			});
		},
		registerSlidesForShortcutKeys(slidesIds) {
			slidesIds.forEach((id) => {
				this.$shortcutKeyRegister({
					uid: this.getUidForShortcutKey(id),
					onActivate: this.onActivate,
					onDeactivate: this.onDeactivate,
					onFocus: this.onFocus,
				});
			});
		},
	},
	watch: {
		currentSlideId(currentSlideId) {
			this.loadTerms(currentSlideId);
		},
		slidesIds(newIds, oldIds) {
			oldIds.forEach((id) => {
				this.$shortcutKeyDeregister(this.getUidForShortcutKey(id));
			});

			if (this.userHasAccess) {
				this.registerSlidesForShortcutKeys(newIds);
			}
		},
	},
	mounted() {
		if (this.currentSlideId) {
			this.loadTerms(this.currentSlideId);
		}
	},
};
</script>
