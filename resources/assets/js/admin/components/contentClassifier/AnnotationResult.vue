<template>
	<span>(id: {{item.id}}) {{item.title}}</span>
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
