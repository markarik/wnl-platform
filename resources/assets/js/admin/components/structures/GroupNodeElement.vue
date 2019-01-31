<template>
	<div class="margin bottom">
		{{ structurable.name }}
	</div>
</template>

<script>
export default {
	name: 'GroupNodeElement',
	props: {
		structurable: {
			required: true,
			type: Object,
		}
	},
};
</script>
