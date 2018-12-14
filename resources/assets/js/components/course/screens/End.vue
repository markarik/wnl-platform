<template>
	<wnl-html :showBacklink="true" :screenData="screenData"></wnl-html>
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
