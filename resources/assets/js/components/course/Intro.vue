<template>
	<h4 class="margin bottom">Witaj {{currentUserName}}! <wnl-emoji name="wave"></wnl-emoji></h4>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import {mapGetters} from 'vuex'

	export default {
		name: 'Intro',
		computed: {
			...mapGetters(['currentUserName']),
		},
	}
</script>
