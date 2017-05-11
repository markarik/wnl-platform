<template>
	<div class="screens-editor">
		<div class="screens-list">
			<wnl-screens-list-item v-for="screen in screens"
				:name="screen.name"
				:id="screen.id">
			</wnl-screens-list-item>
		</div>
		<div class="screen-editor" v-if="screenId">
			Cokolwiek ?
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.screens-editor
		border-top: $border-light-gray
		display: flex
		margin-top: $margin-big
		padding-top: $margin-big

	.admin-left
		border-right: $border-light-gray
		flex: 0 auto
		width: 300px
		padding: $margin-base

	.admin-right
		flex: 8 auto
		padding: $margin-base
</style>

<script>
	import ScreensListItem from 'js/admin/components/lessons/edit/ScreensListItem.vue'

	export default {
		name: 'ScreensEditor',
		components: {
			'wnl-screens-list-item': ScreensListItem,
		},
		data() {
			return {
				screens: [
					{
						id: 1,
						name: 'Screen 1',
					},
					{
						id: 2,
						name: 'Screen 2',
					},
				],
			}
		},
		computed: {
			screenId() {
				return this.$route.params.screenId
			}
		}
	}
</script>
