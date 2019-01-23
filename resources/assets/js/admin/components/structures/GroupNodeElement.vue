<template>
	<div class="margin bottom">
		{{ name }}
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">

</style>

<script>
	export default {
		name: 'GroupNodeElement',
		props: ['structurable'],
		data(){
			return {...this.structurable}
		},
		computed: {
		},
	};
</script>
