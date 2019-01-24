<template>
	<span>(id: {{item.id}}) <span v-html="item.text"></span></span>
</template>

<script>
export default {
	props: {
		item: {
			required: true,
			type: Object,
		},
	},
};
</script>
