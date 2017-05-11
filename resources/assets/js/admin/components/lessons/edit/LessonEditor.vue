<template>
	<div class="lesson-editor">
		<div class="field is-grouped">
			<div class="control">
				<span class="select">
					<select>
						<option v-for="group in groups" :value="group.id">
							{{group.name}}
						</option>
					</select>
				</span>
			</div>
			<div class="control">
				<input type="text" class="input" placeholder="Tytuł lekcji">
			</div>
			<div class="control">
				<a class="button is-success">
					Zapisz
				</a>
			</div>
		</div>
		<wnl-screens-editor></wnl-screens-editor>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	// .field-label
	// 	flex: 0 1 auto
</style>

<script>
	import ScreensEditor from 'js/admin/components/lessons/edit/ScreensEditor.vue'

	export default {
		name: 'LessonEditor',
		components: {
			'wnl-screens-editor': ScreensEditor,
		},
		data() {
			return {
				name: 'Reumatologia',
				group: {
					id: 1,
					name: 'Interna',
				},
				groups: [
					{
						id: 1,
						name: 'Interna',
					},
					{
						id: 2,
						name: 'Pediatria',
					},
					{
						id: 3,
						name: 'Chirurgia',
					},
				],
			}
		},
	}
</script>
