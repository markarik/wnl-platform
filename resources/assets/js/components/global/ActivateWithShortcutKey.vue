<template>
	<div>
		<slot
			:is-active="isActive"
			:is-focused="isFocused"
			:on-update-is-active="onUpdateIsActive"
			:on-component-created="onComponentCreated"
			:on-component-destroyed="onComponentDestroyed"
		></slot>
	</div>
</template>

<script>
import {nextTick} from 'vue';

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
				this.$shortcutKeySetActiveInstance(`activate-with-shortcut-key-${this._uid}`);
				this.isActive = true;
			} else {
				this.$shortcutKeyResetActiveInstance();
			}
		},
		onComponentCreated() {
			this.$shortcutKeyRegister({
				uid: `activate-with-shortcut-key-${this._uid}`,
				onActivate: async () => {
					this.isActive = true;

					await nextTick();
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
		onComponentDestroyed() {
			this.$shortcutKeyDeregister(`activate-with-shortcut-key-${this._uid}`);
		}
	},
};
</script>
