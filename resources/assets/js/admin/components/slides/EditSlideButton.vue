<template>
	<a class="button is-small is-info" @click="editSlide">
		<span>Edytuj tego slajda</span>
	</a>
</template>

<style lang="sass">

</style>

<script>
	export default {
		name: 'EditSlideButton',
		methods: {
			editSlide() {
				console.log('klikl');
			}
		}
	}
</script>
