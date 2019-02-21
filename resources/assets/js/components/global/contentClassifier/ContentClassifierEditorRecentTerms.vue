<template>
	<div class="recent-terms">
		<div
			v-if="lastUsedTerm"
			@click="attachLastUsedTerm"
			:class="{
				clickable: canAttachLastUsedTerm,
				'recent-terms__option': true,
				disabled: !canAttachLastUsedTerm,
				'fit-content': true
			}"
		>
			<div class="recent-terms__group-title">
				<span class="icon is-small">
					<i class="fa fa-tag"></i>
				</span>
				Dodaj ostatnio użyte pojęcie:
			</div>
			<div class="recent-terms__group"><strong>{{lastUsedTerm.tag.name}}</strong></div>
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
			:title="lastUsedTermsDisplay"
		>
			<div class="recent-terms__group-title">
				<span class="icon is-small">
					<i class="fa fa-tags"></i>
				</span>
				Dodaj wszystkie pojęcia ostatnio edytowanego elementu:
			</div>
			<div class="recent-terms__group"><strong>{{lastUsedTermsDisplay}}</strong></div>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.recent-terms
		margin-bottom: $margin-base
		display: flex

		&__group-title
			white-space: nowrap
			overflow: hidden
			text-overflow: ellipsis

		&__group
			white-space: nowrap
			text-overflow: ellipsis
			overflow: hidden
			padding-left: 23px
			font-size: $font-size-minus-1

		&__option
			padding-right: $margin-big
			overflow: hidden

			&.fit-content
				flex-shrink: 0

			&.disabled
				opacity: .3

			.icon
				color: $color-lighter-gray
				margin-right: $margin-small
</style>

<script>
export default {
	props: {
		items: {
			type: Array,
			required: true,
		},
		lastUsedTerm: {
			type: Object,
			default: null,
		},
		lastUsedTermsSet: {
			type: Array,
			default: () => [],
		},
	},
	computed: {
		canAttachLastUsedTerm() {
			return this.lastUsedTerm && !this.isTermAttached(this.lastUsedTerm);
		},
		canAttachLastUsedTermsSet() {
			return this.lastUsedTermsSet && !this.lastUsedTermsSet.every(this.isTermAttached);
		},
		lastUsedTermsDisplay() {
			return this.lastUsedTermsSet.map(term => term.tag.name).join(', ');
		}
	},
	methods: {
		attachLastUsedTerm() {
			if (this.canAttachLastUsedTerm) {
				this.$emit('attachTaxonomyTerm', this.lastUsedTerm);
			}
		},
		attachLastUsedTermsSet() {
			if (this.canAttachLastUsedTermsSet) {
				this.lastUsedTermsSet.forEach(term => this.$emit('attachTaxonomyTerm', term));
			}
		},
		isTermAttached(term) {
			return this.items.every(item => item.taxonomyTerms.find(({id}) => id === term.id));
		}
	},
};
</script>
