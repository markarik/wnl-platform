<template>
		<div class="user-autocomplete-item">
			<div class="autocomplete-box__avatar">
				<wnl-avatar :fullName="item.full_name" :url="item.avatar"></wnl-avatar>
			</div>
			<div v-if="checkForDisplayName" class="autocomplete-box__text">
				{{ nameToDisplay }}
			</div>
			<div v-else class="autocomplete-box__text">
				{{ nameToDisplay.displayName }}
				<div class="autocomplete-box__text__fullName">
					{{ nameToDisplay.fullName }}
				</div>
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
	computed: {
		checkForDisplayName() {
			return this.item.full_name === this.item.display_name;
		},
		nameToDisplay() {
			if (this.checkForDisplayName) {
				return this.item.full_name;
			} else {
				return {
					fullName: this.item.full_name,
					displayName: this.item.display_name
				};
			}
		}
	}
};
</script>
