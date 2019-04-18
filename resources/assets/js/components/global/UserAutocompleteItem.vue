<template>
	<div class="user-autocomplete-item">
		<div class="autocomplete-box__avatar">
			<wnl-avatar :full-name="item.full_name" :url="item.avatar" />
		</div>
		<div class="autocomplete-box__text">
			{{item.full_name}}
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	.user-autocomplete-item
		align-items: center
		cursor: pointer
		display: flex

	.autocomplete-box__text
		padding: 5px 10px
</style>

<script>
export default {
	name: 'UserAutocompleteItem',
	props: ['item'],
};
</script>
