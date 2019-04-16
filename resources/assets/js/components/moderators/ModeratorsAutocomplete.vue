<template>
	<div>
		<div v-if="selected" class="selected-container">
			<span>{{selected.full_name}}</span>
			<a class="button is-primary is-outlined" @click="onChange(null)">
				<span class="icon is-small">
					<i class="fa fa-times" />
				</span>
			</a></div>
		<wnl-autocomplete
			v-else
			v-model="textInputValue"
			:items="usersListFiltered"
			@change="onChange"
		>
			<wnl-user-autocomplete-item slot-scope="slotProps" :item="slotProps.item" />
		</wnl-autocomplete>

	</div>
</template>
<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	.selected-container
		display: flex
		align-items: center

	.button
		border: none
		width: $font-size-minus-2

</style>

<script>
import WnlAutocomplete from 'js/components/global/Autocomplete';
import WnlUserAutocompleteItem from 'js/components/global/UserAutocompleteItem';

export default {
	data() {
		return {
			textInputValue: '',
		};
	},
	props: {
		usersList: {
			type: Array,
			required: true
		},
		selected: {
			type: Object,
			default: () => ({}),
		}
	},
	components: {
		WnlAutocomplete,
		WnlUserAutocompleteItem,
	},
	computed: {
		usersListFiltered() {
			if (this.textInputValue.length === 0) {
				return [];
			}

			return this.usersList.filter(moderator => {
				return moderator.full_name.toLowerCase().indexOf(this.textInputValue.toLowerCase()) > -1;
			});
		},
	},
	methods: {
		onChange(selectedUser) {
			this.textInputValue = '';
			this.$emit('change', selectedUser);
		}
	},
};
</script>
