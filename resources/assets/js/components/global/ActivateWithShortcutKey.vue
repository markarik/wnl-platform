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
import {mapGetters, mapActions} from 'vuex';

import {scrollToElement} from 'js/utils/animations';

export default {
	data() {
		return {
			activateWithShortcutKeyId: `activate-with-shortcut-key-${this._uid}`
		};
	},
	computed: {
		...mapGetters('activateWithShortcutKey', ['isActiveByUid', 'isFocusedByUid']),
		isActive() {
			return this.isActiveByUid(this.activateWithShortcutKeyId);
		},
		isFocused() {
			return this.isFocusedByUid(this.activateWithShortcutKeyId);
		}
	},
	methods: {
		...mapActions('activateWithShortcutKey', ['setActiveInstance', 'resetActiveInstance', 'register', 'deregister']),
		onUpdateIsActive(isActive) {
			if (isActive) {
				this.setActiveInstance(this.activateWithShortcutKeyId);
			} else {
				this.resetActiveInstance();
			}
		},
		onComponentCreated() {
			this.register(this.activateWithShortcutKeyId);
		},
		onComponentDestroyed() {
			this.deregister(this.activateWithShortcutKeyId);
		}
	},
	watch: {
		async isActive() {
			if (this.isActive) {
				await nextTick();
				scrollToElement(this.$el);
			}
		}
	}
};
</script>
