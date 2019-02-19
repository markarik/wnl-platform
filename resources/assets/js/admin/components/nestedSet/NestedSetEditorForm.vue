<template>
	<div>
		<div class="field">
			<label class=" label is-uppercase"><strong>{{parentTitle}}</strong></label>
			<span class="info small">{{parentSubtitle}}</span>
			<slot name="parent-autocomplete" :validate-and-change-parent="validateAndChangeParent"></slot>
		</div>

		<div class="field">
			<label class="label is-uppercase"><strong>{{title}}</strong></label>
			<span class="info">{{subtitle}}</span>
			<slot name="autocomplete"></slot>
		</div>

		<slot name="extra-fields"></slot>
		<div class="has-text-centered">
			<button class="button" @click="onSubmit" :disabled="submitDisabled">{{submitLabel}}</button>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.info
		color: $color-gray

	.field
		margin-bottom: $margin-big
</style>

<script>
import {mapActions} from 'vuex';
import scrollToNodeMixin from 'js/admin/mixins/scroll-to-node';

import {ALERT_TYPES} from 'js/consts/alert';

export default {
	mixins: [scrollToNodeMixin],
	props: {
		parentTitle: {
			type: String,
			required: true,
		},
		parentSubtitle: {
			type: String,
			required: true
		},
		title: {
			type: String,
			required: true
		},
		subtitle: {
			type: String,
			required: true
		},
		submitLabel: {
			type: String,
			default: 'Zapisz'
		},
		submitDisabled: {
			type: Boolean,
			default: false
		},
		vuexModuleName: {
			type: String,
			required: true
		},
		onSave: {
			type: Function,
			required: true
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		getter(getter, payload) {
			const storeGetter = this.$store.getters[`${this.vuexModuleName}/${getter}`];
			if (typeof storeGetter === 'function') {
				return storeGetter(payload);
			}
			return storeGetter;
		},
		action(action, payload = {}) {
			return this.$store.dispatch(`${this.vuexModuleName}/${action}`, payload);
		},
		validateParent(parent, source) {
			if (parent && this.getter('getAncestorsById', parent.id).find(t => t.id === source.id)) {
				this.addAutoDismissableAlert({
					text: 'Nie możesz przenieść elementu do jego potomka.',
					type: ALERT_TYPES.ERROR,
				});
				return false;
			}
			return true;
		},
		validateAndChangeParent(parent, source) {
			if (!this.validateParent(parent, source)) {
				return;
			}

			this.$emit('changeParent', parent);
		},
		async onSubmit() {
			try {
				const node = await this.onSave();

				if (node.parent_id) {
					this.action('expand', node.parent_id);
				} else {
					this.$emit('changeNode');
					this.scrollToNode(node);
				}

				this.addAutoDismissableAlert({
					text: 'Zapisano!',
					type: 'success'
				});
			} catch (error) {
				$wnl.logger.capture(error);

				this.addAutoDismissableAlert({
					text: 'Ups, coś poszło nie tak, spróbuj ponownie.',
					type: 'error',
				});
			}
		}
	},
};
</script>
