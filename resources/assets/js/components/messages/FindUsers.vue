<template lang="html">
	<div class="wnl-find-users">

		<div class="wnl-find-users-input">
			<input
				:placeholder="$t('messages.search.placeholder')"
				@input="onInput"
				ref="input"
			/>
		</div>

		<div class="wnl-find-users__list">

				<wnl-conversation-snippet
					v-for="item in results"
					class="wnl-find-users__item"
					@click="onItemChosen(item)"
					:class="{ active: item.active }"
					:profiles="item"
					:key="item.id"
				/>

		</div>

	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'


	.wnl-find-users
		&-input
			display: flex
			align-items: center

			input
				+simple-input
				width: 90%
				margin: auto
				text-align: left

				&:focus
					outline: none

		&__list
			display: flex
			flex-direction: column

		&__item

</style>

<script>
	import _ from 'lodash'
	import axios from 'axios'
	import {getApiUrl} from 'js/utils/env'
	import autocomplete from 'js/mixins/autocomplete-nav'
	import ConversationSnippet from 'js/components/messages/ConversationSnippet'

	export default {
		name: 'FindUsers',
		components: {
			'wnl-conversation-snippet': ConversationSnippet
		},
		mixins: [autocomplete],
		data() {
			return {
				results: [],
			}
		},
		methods: {
			onInput: _.debounce(function ({target: {value}}) {
				if (value.length === 0) return

				const query = encodeURIComponent(value)
				axios.get(getApiUrl(`user_profiles/.search?q=${query}`))
					.then(res => {
						if (res.data.length === 0) return
						this.results = res.data
					})
			}, 300),
			onClose(){},
			itemChosen(){},
		},
		mounted(){
			this.$refs.input.focus()
		}
	}
</script>