<template>
	<svg><use :xlink:href="href"/></svg>
</template>

<script>
import { getSvgUrl } from 'js/utils/env';
export default {
	props: ['name'],
	computed: {
		href() {
			return getSvgUrl(`icons/sprite.svg#${this.name}`);
		}
	}
};
</script>
