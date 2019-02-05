<template>
	<div v-if="canAccess" class="content-item-classifier">
		<div v-if="expanded" class="content-item-classifier__editor">
			<div class="content-item-classifier__editor__header">
				<div>
					<span class="content-item-classifier__tag-icon icon is-small"><i class="fa fa-tags"></i></span>
					<strong>{{CONTENT_TYPE_NAMES[contentItem.type]}} #{{contentItem.id}}</strong>
				</div>
				<span class="content-item-classifier__collapse-icon icon is-small clickable" @click="expanded=false"><i class="fa fa-chevron-up"></i></span>
			</div>
			<wnl-content-classifier-editor
				:items="[contentItem]"
				@onTaxonomyTermAttached="onTaxonomyTermAttached"
				@onTaxonomyTermDetached="onTaxonomyTermDetached"
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
// TODO handle updated item
import {mapGetters} from 'vuex';

// TODO don't include admin components
import WnlContentClassifierEditor from 'js/admin/components/contentClassifier/ContentClassifierEditor';

const CONTENT_TYPE_NAMES = {
	flashcards: 'Pytanie otwarte',
	slides: 'Slajd',
	quizQuestions: 'Pytanie z bazy pytań',
	annotations: 'Przypis'
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
			this.$emit('onTaxonomyTermAttached', term);
		},
		onTaxonomyTermDetached(term) {
			this.$emit('onTaxonomyTermDetached', term);
		},
	},
};
</script>
