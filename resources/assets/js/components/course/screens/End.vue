<template>
	<wnl-html :show-backlink="true" :screen-data="screenData" />
</template>

<script>
import Html from './Html';

export default {
	name: 'End',
	components: {
		'wnl-html': Html,
	},
	props: ['screenData']
};
</script>
