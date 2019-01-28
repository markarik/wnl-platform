<template>
	<div>
		<h2 class="title is-2">Edycja struktury kursu <span v-if="isEdit">(Id: {{id}})</span></h2>

		<wnl-structure-nodes-editor :taxonomyId="id" v-if="isEdit" />
	</div>
</template>

<script>
import WnlStructureNodesEditor from 'js/admin/components/structure/StructureNodeEditor';
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
		WnlStructureNodesEditor,
	},
	methods: {
	},
};
</script>
