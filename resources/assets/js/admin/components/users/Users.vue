<template>
	<div>
		<autocomplete
			:items="autocompleteItems"
			:itemComponent="'wnl-user-autocomplete-item'"
		></autocomplete>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
</style>

<script>
	import Autocomplete from 'js/components/global/Autocomplete'

	export default {
		name: 'Users',
		components: {
			'autocomplete': Autocomplete,
		},
	}
</script>
