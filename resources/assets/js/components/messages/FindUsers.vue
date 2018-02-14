<template lang="html">
	<div class="wnl-find-users">
		<input
			:placeholder="$t('messages.search.placeholder')"
			@input="onInput"
		/>
		<wnl-autocomplete
			:onItemChosen="itemChosen"
			:itemComponent="'wnl-user-autocomplete-item'"
			:items="usersList"
			@close="onClose"
			class="wnl-user-search__dropdown"
			ref="autocomplete"
		/>
	</div>
</template>

<style lang="sass">
	.wnl-find-users
		input
			width: 100%

		.wnl-find-users__dropdown
			ul
				width: 100%
</style>

<script>
	import _ from 'lodash'
	import axios from 'axios'
	import {getApiUrl} from 'js/utils/env'
	import Autocomplete from 'js/components/global/Autocomplete'

	export default {
		name: 'FindUsers',
		components: {
			'wnl-autocomplete': Autocomplete
		},
		data() {
			return {
				usersList: [],
			}
		},
		methods: {
			onInput: _.debounce(function ({target: {value}}) {
				if (value.length === 0) return

				const query = encodeURIComponent(value)
				axios.get(getApiUrl(`user_profiles/.search?q=${query}`))
					.then(res => {
						if (res.data.length === 0) return
						this.usersList = res.data
					})
			}, 300),
			onClose(){},
			itemChosen(){},
		},
	}
</script>