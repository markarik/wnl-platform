<template lang="html">
	<div>
	 <router-link :to="{ name: 'messages' }">
		<span class="icon">
			<i class="fa fa-comment"></i>
		</span>
	 </router-link>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.icon
		color: $color-gray-dimmed

		&:hover
			cursor: pointer
</style>

<script>
	export default {
		
	}
</script>