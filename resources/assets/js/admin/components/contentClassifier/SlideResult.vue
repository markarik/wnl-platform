<template>
	<wnl-result-item :is-active="isActive" @click="$emit('click', $event)">
		<wnl-slide-thumb
			:slide="item"
		>
			{{item.id}}
		</wnl-slide-thumb>
	</wnl-result-item>
</template>

<script>
import WnlSlideThumb from 'js/components/course/SlideThumb';
import WnlResultItem from 'js/admin/components/contentClassifier/ResultItem';

export default {
	components: {
		WnlSlideThumb,
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
	}
};
</script>
