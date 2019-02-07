<template>
	<div v-if="canAccess && hasContentItem" class="content-item-classifier">
		<div v-if="isAlwaysActive || isActive" class="content-item-classifier__editor">
			<div
				v-if="!isAlwaysActive"
				class="content-item-classifier__editor__header clickable"
				@click="collapse"
			>
				<div>
					<span class="content-item-classifier__tag-icon icon is-small"><i class="fa fa-tags"></i></span>
					<strong>{{CONTENT_TYPE_NAMES[contentItem.type]}} #{{contentItem.id}}</strong>
				</div>
				<span class="content-item-classifier__collapse-icon icon is-small">
					<i class="fa fa-chevron-up"></i>
				</span>
			</div>
			<wnl-content-classifier-editor
				v-if="hasContentItem"
				:items="[contentItem]"
				@taxonomyTermAttached="onTaxonomyTermAttached"
				@taxonomyTermDetached="onTaxonomyTermDetached"
			/>
		</div>
		<div v-else class="clickable content-item-classifier__tag-names" @click="expand">
			<span class="content-item-classifier__tag-icon icon is-small"><i class="fa fa-tags"></i></span>
			<span v-if="hasTaxonomyTerms">{{contentItem.taxonomyTerms.map(term => term.tag.name).join(', ')}}</span>
			<span v-else>brak</span>
		</div>
	</div>
	<wnl-text-loader v-else-if="canAccess && !hasContentItem"></wnl-text-loader>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.content-item-classifier
		width: 100%

		&__editor
			background-color: $color-background-lighter-gray
			border: $border-light-gray
			padding: $margin-medium
			&__header
				display: flex
				justify-content: space-between
				margin-bottom: $margin-base

		&__tag-names
			color: $color-lighter-gray

		&__tag-icon
			color: $color-lighter-gray
			margin-right: $margin-small

		&__collapse-icon
			color: $color-lighter-gray
			padding: $margin-tiny

</style>

<script>
import {mapGetters, mapActions, mapMutations} from 'vuex';
import WnlContentClassifierEditor from 'js/components/global/contentClassifier/ContentClassifierEditor';
import {CONTENT_TYPES} from 'js/consts/contentClassifier';
import {CONTENT_CLASSIFIER_ATTACH_TERM, CONTENT_CLASSIFIER_DETACH_TERM} from 'js/store/mutations-types';

const CONTENT_TYPE_NAMES = {
	[CONTENT_TYPES.FLASHCARD]: 'Pytanie otwarte',
	[CONTENT_TYPES.SLIDE]: 'Slajd',
	[CONTENT_TYPES.QUIZ_QUESTION]: 'Pytanie z bazy pytań',
	[CONTENT_TYPES.ANNOTATION]: 'Przypis'
};

export default {
	components: {
		WnlContentClassifierEditor,
	},
	data() {
		return {
			CONTENT_TYPE_NAMES,
		};
	},
	props: {
		activateWithShortcutKey: {
			type: Object,
			default: null,
		},
		contentItemId: {
			type: [Number, String],
			required: true,
		},
		contentItemType: {
			type: String,
			required: true
		},
		isActive: {
			type: Boolean,
			required: true,
		},
		isAlwaysActive: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		...mapGetters('contentClassifier', ['getContentItem', 'canAccess']),
		contentItem() {
			return this.getContentItem({contentItemType: this.contentItemType, contentItemId: this.contentItemId}) || {};
		},
		hasContentItem() {
			return this.contentItem.id;
		},
		hasTaxonomyTerms() {
			return this.contentItem.taxonomyTerms && this.contentItem.taxonomyTerms.length > 0;
		},
	},
	methods: {
		...mapActions('contentClassifier', ['fetchTaxonomyTerms']),
		...mapMutations('contentClassifier', {
			attachTerm: CONTENT_CLASSIFIER_ATTACH_TERM,
			detachTerm: CONTENT_CLASSIFIER_DETACH_TERM
		}),
		collapse() {
			this.$parent.$emit('updateIsActive', false);
		},
		expand() {
			this.$parent.$emit('updateIsActive', true);
		},
		onTaxonomyTermAttached(term) {
			this.attachTerm({
				term,
				contentItem: this.contentItem
			});
		},
		onTaxonomyTermDetached(term) {
			this.detachTerm({
				term,
				contentItem: this.contentItem
			});
		},
	},
};
</script>
