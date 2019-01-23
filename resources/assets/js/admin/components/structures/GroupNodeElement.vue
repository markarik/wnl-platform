<template>
	<div class="margin bottom">
		{{ structurable.name }}
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">

</style>

<script>
export default {
	name: 'GroupNodeElement',
	props: {
		structurable :{
			required: true,
			type: Object,
		}
	},
};
</script>
