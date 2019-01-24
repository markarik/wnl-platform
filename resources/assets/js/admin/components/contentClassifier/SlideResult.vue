<template>
	<wnl-slide-thumb
		:slide="item"
	>
		{{item.id}}
	</wnl-slide-thumb>
</template>

<style lang="sass">
</style>

<script>
import WnlSlideThumb from 'js/components/course/SlideThumb';

export default {
	props: {
		item: {
			required: true,
			type: Object,
		},
	},
	components: {
		WnlSlideThumb,
	}
};
</script>
