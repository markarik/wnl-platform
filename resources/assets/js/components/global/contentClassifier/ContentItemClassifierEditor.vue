<template>
	<div v-if="canAccess" class="content-item-classifier">
		<div v-if="alwaysExpanded || expanded" class="content-item-classifier__editor">
			<div
				v-if="!alwaysExpanded"
				class="content-item-classifier__editor__header clickable"
				@click="expanded=false"
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
				:items="[contentItem]"
				@taxonomyTermAttached="onTaxonomyTermAttached"
				@taxonomyTermDetached="onTaxonomyTermDetached"
			/>
		</div>
		<div v-else class="clickable content-item-classifier__tag-names" @click="expanded=true">
			<span class="content-item-classifier__tag-icon icon is-small"><i class="fa fa-tags"></i></span>
			<span v-if="hasTaxonomyTerms">{{contentItem.taxonomyTerms.map(term => term.tag.name).join(', ')}}</span>
			<span v-else>brak</span>
		</div>
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
import {mapGetters} from 'vuex';
import WnlContentClassifierEditor from 'js/components/global/contentClassifier/ContentClassifierEditor';
import {CONTENT_TYPES} from 'js/consts/contentClassifier';

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
			expanded: false,
			CONTENT_TYPE_NAMES,
		};
	},
	props: {
		alwaysExpanded: {
			type: Boolean,
			default: false,
		},
		contentItem: {
			type: Object,
			required: true,
		}
	},
	computed: {
		...mapGetters(['isAdmin', 'isModerator']),
		canAccess() {
			return this.isAdmin || this.isModerator;
		},
		hasTaxonomyTerms() {
			return this.contentItem.taxonomyTerms && this.contentItem.taxonomyTerms.length > 0;
		}
	},
	methods: {
		onTaxonomyTermAttached(term) {
			this.$emit('taxonomyTermAttached', term);
		},
		onTaxonomyTermDetached(term) {
			this.$emit('taxonomyTermDetached', term);
		},
	},
};
</script>
