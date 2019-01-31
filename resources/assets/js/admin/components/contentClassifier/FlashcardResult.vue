<template>
	<span>(id: {{item.id}}) {{item.content}}</span>
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
