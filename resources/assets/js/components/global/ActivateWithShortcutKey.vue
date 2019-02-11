<template>
	<div>
		<slot
			:is-active="isActive"
			:trigger-blur="triggerBlur"
			:trigger-focus="triggerFocus"
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
			triggerBlur: false,
			triggerFocus: false,
		};
	},
	created() {
		this.$on('updateIsActive', (isActive) => {
			if (isActive) {
				this.$setActiveInstanceFixName(`activate-with-shortcut-key-${this._uid}`);
				this.isActive = true;
			} else {
				this.$resetActiveInstanceFixName();
			}
		});

		this.$on('resetTriggerBlur', () => {
			this.triggerBlur = false;
		});

		this.$on('resetTriggerFocus', () => {
			this.triggerFocus = false;
		});
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
				this.triggerBlur = true;
			},
			onFocus: () => {
				this.triggerFocus = true;
			},
		});
	},
	beforeDestroy() {
		this.$deregisterFixName(`activate-with-shortcut-key-${this._uid}`);
	},
};
</script>
