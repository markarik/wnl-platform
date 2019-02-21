<template>
	<wnl-html show-backlink :screen-data="screenData"></wnl-html>
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
