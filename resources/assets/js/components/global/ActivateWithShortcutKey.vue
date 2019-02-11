<template>
	<div>
		<slot
			:is-active="isActive"
			:is-focused="isFocused"
			:on-update-is-active="onUpdateIsActive"
		></slot>
	</div>
</template>

<script>
import Vue from 'vue';

import {scrollToElement} from 'js/utils/animations';

export default {
	data() {
		return {
			isActive: false,
			isFocused: false,
		};
	},
	methods: {
		onUpdateIsActive(isActive) {
			if (isActive) {
				this.$setActiveInstanceFixName(`activate-with-shortcut-key-${this._uid}`);
				this.isActive = true;
			} else {
				this.$resetActiveInstanceFixName();
			}
		}
	},
	mounted() {
		this.$registerFixName({
			uid: `activate-with-shortcut-key-${this._uid}`,
			onActivate: async () => {
				this.isActive = true;

				await Vue.nextTick();
				scrollToElement(this.$el, 150, 500);
			},
			onDeactivate: () => {
				this.isActive = false;
				this.isFocused = false;
			},
			onFocus: () => {
				this.isFocused = true;
			},
		});
	},
	beforeDestroy() {
		this.$deregisterFixName(`activate-with-shortcut-key-${this._uid}`);
	},
};
</script>
