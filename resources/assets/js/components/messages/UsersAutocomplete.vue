<template lang="html">

	<div class="wnl-users-autocomplete">
		<wnl-conversation-snippet
			v-for="(item, index) in items"
			class="wnl-users-autocomplete__item"
			:class="{ active: item.active }"
			:profiles="[item, currentUser]"
			:key="item.id"
			:ref="item.id"
			@close="onClose"
			:bothNames="true"
		/>
	</div>

</template>

<style lang="sass">
	.wnl-users-autocomplete
		display: flex
		flex-direction: column
		overflow-x: hidden
		overflow-y: auto

</style>

<script>
	import autocomplete from 'js/mixins/autocomplete-nav'
	import ConversationSnippet from 'js/components/messages/ConversationSnippet'
	import {mapGetters} from 'vuex'

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
		computed: {
			...mapGetters(['currentUser'])
		},
		methods: {
			onClose() {
				this.$emit('close')
			},
			onItemChosen(item) {
				this.$emit('close')
				this.$refs[item.id][0].$el.click()
			}
		}
	}
</script>
