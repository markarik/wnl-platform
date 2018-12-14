<template lang="html">
	<svg><use :xlink:href="href"/></svg>
</template>

<script>
import { getImageUrl } from 'js/utils/env';
export default {
	props: ['name'],
	computed: {
		href() {
			return getImageUrl(`icons/sprite.svg#${this.name}`);
		}
	}
};
</script>
