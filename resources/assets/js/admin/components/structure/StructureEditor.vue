<template>
	<div>
		<h2 class="title is-2">Edycja struktury kursu</h2>

		<wnl-structure-nodes-editor :courseId="courseId" v-if="isEdit" />
	</div>
</template>

<script>
import WnlStructureNodesEditor from 'js/admin/components/structure/StructureNodeEditor';
import {getApiUrl} from 'js/utils/env';
import {ALERT_TYPES} from 'js/consts/alert';

export default {
	props: {
		courseId: {
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
};
</script>
