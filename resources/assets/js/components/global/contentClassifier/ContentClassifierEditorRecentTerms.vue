<template>
	<div class="recent-terms">
		<div
			v-if="lastUsedTerm"
			@click="attachLastUsedTerm"
			:class="{
				clickable: canAttachLastUsedTerm,
				'recent-terms__option': true,
				disabled: !canAttachLastUsedTerm,
			}"
		>
			<span class="icon is-small">
				<i class="fa fa-tag"></i>
			</span>
			Dodaj ostatnio użyte pojęcie: <strong>{{lastUsedTerm.tag.name}}</strong>
		</div>
		<div
			v-if="lastUsedTermsSet"
			@click="attachLastUsedTermsSet"
			:class="{
				clickable: canAttachLastUsedTermsSet,
				'recent-terms__option': true,
				content: true,
				disabled: !canAttachLastUsedTermsSet,
			}"
		>
			<span class="icon is-small">
				<i class="fa fa-tags"></i>
			</span>
			Dodaj wszystkie pojęcia ostatnio edytowanego elementu:
			<ul>
				<li v-for="term in lastUsedTermsSet" :key="term.id"><strong>{{term.tag.name}}</strong></li>
			</ul>
		</div>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.recent-terms
		display: flex
		&__option
			flex: 1
			margin: 0 $margin-medium

			&.disabled
				opacity: .3

			.icon
				color: $color-lighter-gray
				margin-right: $margin-small
</style>

<script>
import contentClassifierStore from 'js/services/contentClassifierStore';
import {CONTENT_CLASSIFIER_STORE_KEYS} from 'js/services/contentClassifierStore';

export default {
	data() {
		return {
			lastUsedTerm: contentClassifierStore.get(CONTENT_CLASSIFIER_STORE_KEYS.LAST_TERM),
			lastUsedTermsSet: contentClassifierStore.get(CONTENT_CLASSIFIER_STORE_KEYS.ALL_TERMS),
		};
	},
	props: {
		items: {
			type: Array,
			required: true,
		},
		triggerAttachLastUsedTerm: {
			type: Boolean,
			default: false,
		},
		triggerAttachLastUsedTermsSet: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		canAttachLastUsedTerm() {
			return this.lastUsedTerm && !this.isTermAttached(this.lastUsedTerm);
		},
		canAttachLastUsedTermsSet() {
			return this.lastUsedTermsSet && !this.lastUsedTermsSet.every(this.isTermAttached);
		},
	},
	methods: {
		attachLastUsedTerm() {
			if (this.canAttachLastUsedTerm) {
				this.$emit('attachTaxonomyTerm', this.lastUsedTerm);
			}
		},
		attachLastUsedTermsSet() {
			if (this.canAttachLastUsedTermsSet) {
				this.lastUsedTermsSet.map(term => this.$emit('attachTaxonomyTerm', term));
			}
		},
		isTermAttached(term) {
			return this.items.every(item => item.taxonomyTerms.find(({id}) => id === term.id));
		}
	},
	watch: {
		async triggerAttachLastUsedTerm(triggerAttachLastUsedTerm) {
			if (triggerAttachLastUsedTerm) {
				this.attachLastUsedTerm();
				this.$emit('attachLastUsedTermTriggered');
			}
		},
		triggerAttachLastUsedTermsSet(triggerAttachLastUsedTermsSet) {
			if (triggerAttachLastUsedTermsSet) {
				this.attachLastUsedTermsSet();
				this.$emit('attachLastUsedTermsSetTriggered');
			}
		},
	},
};
</script>
