<template>
	<div v-if="canAccess" class="content-item-classifier">
		<div v-if="expanded" class="content-item-classifier__editor">
			<span class="icon is-small clickable" @click="expanded=false"><i class="fa fa-chevron-up"></i></span>
			<wnl-content-item-classifier-editor :filtered-content="[contentItem]" />
		</div>
		<div v-else class="clickable" @click="expanded=true">
			<span class="icon"><i class="fa fa-tags"></i></span> asd TODO
		</div>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.content-item-classifier
		width: 100%

		&__editor
			background-color: $color-background-light-gray
			border: $border-dark-gray

</style>

<script>
import {mapActions, mapGetters, mapState} from 'vuex';

// TODO don't include admin components
import WnlContentItemClassifierEditor from 'js/admin/components/contentClassifier/ContentClassifierEditor';

export default {
	components: {
		WnlContentItemClassifierEditor,
	},
	data() {
		return {
			expanded: false,
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
	},
	methods: {

	},
};
</script>
