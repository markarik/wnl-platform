<template>
	<div class="recent-terms">
		<div
			v-if="lastUsedTerm"
			@click="canAddLastUsedTerm && $emit('attachTaxonomyTerm', lastUsedTerm)"
			:class="{
				clickable: canAddLastUsedTerm,
				'recent-terms__option': true,
				disabled: !canAddLastUsedTerm,
			}"
		>
			<span class="icon is-small">
				<i class="fa fa-tag"></i>
			</span>
			Dodaj ostatnio użyte pojęcie: <strong>{{lastUsedTerm.tag.name}}</strong>
		</div>
		<div
			v-if="lastUsedTermsSet"
			@click="canAddLastUsedTermsSet && lastUsedTermsSet.map(term => $emit('attachTaxonomyTerm', term))"
			:class="{
				clickable: canAddLastUsedTermsSet,
				'recent-terms__option': true,
				content: true,
				disabled: !canAddLastUsedTermsSet,
			}"
		>
			<span class="icon is-small">
				<i class="fa fa-tags"></i>
			</span>
			Dodaj wszystkie pojęcia ostatnio edytownego elementu:
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
		margin-bottom: $margin-base
		&__option
			flex-basis: 50%
			margin-right: $margin-base

			&.disabled
				opacity: .3

			.icon
				color: $color-lighter-gray
</style>

<script>
import contentClassifierStore from 'js/services/contentClassifierStore';
import {CONTENT_CLASSIFIER_KEYS} from 'js/services/contentClassifierStore';

export default {
	components: {
	},
	data() {
		return {};
	},
	props: {
		items: {
			type: Array,
			required: true,
		}
	},
	computed: {
		lastUsedTerm: {
			get() {
				return contentClassifierStore.get(CONTENT_CLASSIFIER_KEYS.LAST_TERM);
			},
			set(term) {
				// TODO fix reactivity
				contentClassifierStore.set(CONTENT_CLASSIFIER_KEYS.LAST_TERM, term);
			}
		},
		lastUsedTermsSet: {
			get() {
				return contentClassifierStore.get(CONTENT_CLASSIFIER_KEYS.ALL_TERMS);
			},
			set(terms) {
				contentClassifierStore.set(CONTENT_CLASSIFIER_KEYS.ALL_TERMS, terms);
			},
		},
		canAddLastUsedTerm() {
			return this.lastUsedTerm && !this.items.every(item => item.taxonomyTerms.find(term => term.id === this.lastUsedTerm.id));
		},
		canAddLastUsedTermsSet() {
			return this.lastUsedTermsSet && !this.lastUsedTermsSet.every(lastUsedTerm => this.items.every(item => item.taxonomyTerms.find(term => term.id === lastUsedTerm.id)));
		},
	},
	methods: {

	}
};
</script>
