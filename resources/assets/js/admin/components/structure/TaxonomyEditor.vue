<template>
	<div>
		<h2 class="title is-2">Edycja taksonomii <span v-if="isEdit">(Id: {{id}})</span></h2>

		<wnl-taxonomy-terms-editor :taxonomyId="id" v-if="isEdit" />
	</div>
</template>

<script>
import {mapActions} from 'vuex';
import WnlTaxonomyTermsEditor from 'js/admin/components/structure/TaxonomyTermsEditor';
import {getApiUrl} from 'js/utils/env';
import {ALERT_TYPES} from 'js/consts/alert';

export default {
	props: {
		id: {
			type: [String, Number],
			required: true,
		},
	},
	computed: {
		isEdit() {
			return this.id !== 'new';
		},
	},
	components: {
		WnlTaxonomyTermsEditor,
	},
	methods: {
	},
};
</script>
