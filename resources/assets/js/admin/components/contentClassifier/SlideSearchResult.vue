<template>
	<wnl-slide-search-result
		:hit="{_source: item}"
	/>
</template>

<style lang="sass">
</style>

<script>
import WnlSlideSearchResult from 'js/components/global/search/SlideSearchResult';

export default {
	props: {
		item: {
			required: true,
			type: Object,
		},
	},
	components: {
		WnlSlideSearchResult,
	}
};
</script>
