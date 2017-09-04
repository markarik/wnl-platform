<template>
		<div class="user-autocomplete-item">
			<div class="autocomplete-box__avatar">
				<wnl-avatar :fullName="item.full_name" :url="item.avatar"></wnl-avatar>
			</div>
			<div class="autocomplete-box__text">
				{{ item.full_name }}
			</div>
		</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	.user-autocomplete-item
		cursor: pointer
		display: flex
</style>

<script>
	export default {
		name: 'UserAutocompleteItem',
		props: ['item']
	}
</script>
