<template>
	<div v-if="canAccess" class="content-item-classifier" tabindex="-1">
		<template v-if="hasContentItem">
			<div v-if="isAlwaysActive || isActive" class="content-item-classifier__editor">
				<div
					v-if="!isAlwaysActive"
					class="content-item-classifier__editor__header clickable"
					@click="updateIsActive(!isActive)"
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
			<div v-else class="clickable content-item-classifier__tag-names" @click="updateIsActive(true)">
				<span class="content-item-classifier__tag-icon icon is-small"><i class="fa fa-tags"></i></span>
				<span v-if="hasTaxonomyTerms">{{contentItem.taxonomyTerms.map(term => term.tag.name).join(', ')}}</span>
				<span v-else>brak</span>
			</div>
		</template>
		<div v-else-if="isError">
			<span class="content-item-classifier__tag-icon icon is-small"><i class="fa fa-exclamation"></i></span>
			Ups, nie udalo się załadować przypisanych pojęć. Odśwież stronę, aby spróbować ponownie.
		</div>
		<wnl-text-loader v-else></wnl-text-loader>
	</div>
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
import {mapGetters, mapMutations} from 'vuex';
import WnlContentClassifierEditor from 'js/components/global/contentClassifier/ContentClassifierEditor';
import {CONTENT_TYPES} from 'js/consts/contentClassifier';
import {CONTENT_CLASSIFIER_ATTACH_TERM, CONTENT_CLASSIFIER_DETACH_TERM} from 'js/store/mutations-types';
import {REQUEST_STATES} from 'js/consts/state';

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
			default: false,
		},
		isAlwaysActive: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		...mapGetters('contentClassifier', ['getContentItem', 'canAccess', 'getContentItemState']),
		contentItem() {
			return this.getContentItem({contentItemType: this.contentItemType, contentItemId: this.contentItemId}) || null;
		},
		hasContentItem() {
			return this.contentItem;
		},
		hasTaxonomyTerms() {
			return this.contentItem.taxonomyTerms && this.contentItem.taxonomyTerms.length > 0;
		},
		isError() {
			return this.getContentItemState({contentItemType: this.contentItemType, contentItemId: this.contentItemId}) === REQUEST_STATES.ERROR;
		},
	},
	methods: {
		...mapMutations('contentClassifier', {
			attachTerm: CONTENT_CLASSIFIER_ATTACH_TERM,
			detachTerm: CONTENT_CLASSIFIER_DETACH_TERM
		}),
		updateIsActive(isActive) {
			this.$parent.$emit('updateIsActive', isActive);
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
	watch: {
		isActive(isActive) {
			if (!this.isAlwaysActive && isActive) {
				this.$el.focus();
			}
		}
	},
};
</script>
