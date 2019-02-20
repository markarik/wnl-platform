<template>
	<wnl-result-item :is-active="isActive" @click="$emit('click', $event)">
		<span slot="top-left">{{item.id}}</span>
		<span>{{item.title}}</span>
	</wnl-result-item>
</template>

<script>
import WnlResultItem from 'js/admin/components/contentClassifier/ResultItem';

export default {
	components: {
		WnlResultItem,
	},
	props: {
		isActive: {
			type: Boolean,
			default: false,
		},
		item: {
			required: true,
			type: Object,
		},
	},
};
</script>
