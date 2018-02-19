<template lang="html">

	<div class="wnl-users-autocomplete">
		<wnl-conversation-snippet
			v-for="item in items"
			class="wnl-users-autocomplete__item"
			:class="{ active: item.active }"
			:profiles="item"
			:key="item.id"
			:ref="item.id"
		/>
	</div>

</template>

<style lang="sass">
	.wnl-users-autocomplete
		display: flex
		flex-direction: column
		overflow-y: auto

</style>

<script>
	import autocomplete from 'js/mixins/autocomplete-nav'
	import ConversationSnippet from 'js/components/messages/ConversationSnippet'

	export default {
		name: 'UsersAutocomplete',
		props: {
			items: {
				default: () => []
			},
		},
		components: {
			'wnl-conversation-snippet': ConversationSnippet
		},
		mixins: [autocomplete],
		methods: {
			onItemChosen(item) {
				this.$refs[item.id][0].$el.click()
			}
		},
	}
</script>