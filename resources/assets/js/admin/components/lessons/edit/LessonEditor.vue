<template>
	<div class="lesson-editor">
		<p class="title is-4">Edytor lekcji</p>
		<div class="screens-list">
			<wnl-screens-list-item v-for="screen in screens"
				:name="screen.name"
				:id="screen.id">
			</wnl-screens-list-item>
		</div>
		<div class="screen-editor" v-if="screenId">
			<router-view></router-view>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import ScreenEditor from 'js/admin/components/lessons/edit/ScreenEditor.vue'
	import ScreensListItem from 'js/admin/components/lessons/edit/ScreensListItem.vue'

	export default {
		name: 'LessonEditor',
		components: {
			'wnl-screen-editor': ScreenEditor,
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
