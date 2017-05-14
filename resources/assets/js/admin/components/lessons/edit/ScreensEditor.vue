<template>
	<div class="screens-editor">
		<div class="screens-list">
			<p class="title is-5">Ekrany</p>
			<wnl-screens-list-item v-for="screen in screens"
				:name="screen.name"
				:id="screen.id">
			</wnl-screens-list-item>
		</div>
		<div class="screen-editor" v-if="loaded">
			<p class="title is-5">{{currentScreen.name}}</p>

			<!-- Screen meta -->
			<div class="field is-grouped">
				<div class="control">
					<span class="select">
						<select>
							<option v-for="type in types" :value="type">
								{{type}}
							</option>
						</select>
					</span>
				</div>
				<div class="control">
					<input type="text" class="input" placeholder="Tytuł ekranu" v-model="currentScreen.name">
				</div>
				<div class="control">
					<a class="button is-success">
						Zapisz
					</a>
				</div>
			</div>

			<!-- Screen content -->
			<div class="columns content-editor">
				<div class="column">
					<quill :options="{ theme: 'snow' }">
					</quill>
				</div>
				<div class="column is-narrow">
					<!-- <div class="content-editor-preview" v-html="code">
					</div> -->
				</div>
			</div>
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

		.title.is-5
			border-bottom: $border-light-gray
			padding-bottom: $margin-base

	.screens-list
		border-right: $border-light-gray
		padding: $margin-base
		width: 200px

	.screen-editor
		flex: 8 auto
		padding: $margin-base

	.content-editor
		margin-top: $margin-big

	.content-editor-code
		height: 100%
		width: 100%
</style>

<script>
	import ScreensListItem from 'js/admin/components/lessons/edit/ScreensListItem.vue'
	import Quill from 'js/components/global/Quill.vue'
	import _ from 'lodash'
	import Brace from 'vue-bulma-brace'
	import { set } from 'vue'

	export default {
		name: 'ScreensEditor',
		components: {
			'brace': Brace,
			'quill': Quill,
			'wnl-screens-list-item': ScreensListItem,
		},
		data() {
			return {
				code: '',
				currentScreen: {},
				types: [
					'html',
					'quiz',
					'slideshow',
					'end',
				],
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
			loaded() {
				return !_.isEmpty(this.currentScreen)
			}
		},
		methods: {
			setCurrentScreen() {
				set(this, 'currentScreen', this.screens.filter((screen) => {
					return screen.id === this.$route.params.screenId
				})[0])
			},
		},
		mounted() {
			this.setCurrentScreen()
		},
		watch: {
			'$route': 'setCurrentScreen'
		}
	}
</script>
