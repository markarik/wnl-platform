<template>
		<div class="tag-autocomplete-item">{{item.name}}</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
</style>

<script>
export default {
	name: 'TagAutocompleteItem',
	props: ['item']
};
</script>
