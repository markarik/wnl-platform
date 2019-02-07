<template>
	<div>
		<slot :is-active="isActive"></slot>
	</div>
</template>

<script>
import Vue from 'vue';
import {mapActions, mapGetters} from 'vuex';

import {scrollToElement} from 'js/utils/animations';

export default {
	computed: {
		...mapGetters('activateWithShortcutKey', ['activeInstance']),
		isActive() {
			return this.activeInstance === this._uid;
		}
	},
	methods: {
		...mapActions('activateWithShortcutKey', [
			'register',
			'resetActiveInstance',
			'setActiveInstance',
			'unregister'
		]),
		onUpdateIsActive(isActive) {
			if (isActive) {
				this.setActiveInstance(this._uid);
			} else {
				this.resetActiveInstance();
			}
		}
	},
	mounted() {
		this.register(this._uid);
	},
	beforeDestroy() {
		this.unregister(this._uid);
	},
	watch: {
		async isActive(isActive) {
			if (isActive) {
				await Vue.nextTick();
				scrollToElement(this.$el, 150, 500);
			}
		}
	},
};
</script>
