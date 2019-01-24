<template>
	<span>(id: {{item.id}}) <span v-html="item.text"></span></span>
</template>

<style lang="sass">
</style>

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
