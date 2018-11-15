<template>
	<div>
		<wnl-form>
			<wnl-quill
				ref="editor"
				name="text"
				id="elo-elo"
				:options="quillOptions"
				:keyboard="keyboard"
				:toolbar="toolbar"
				:allowMentions="true"
				@input="onInput"
			></wnl-quill>
		</wnl-form>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
</style>

<script>
	import { fontColors } from 'js/utils/colors'
	import Input from 'js/admin/components/forms/Input'
	import Form from 'js/components/global/form/Form'
	import Quill from 'js/components/global/form/Quill'

	export default {
		name: 'Users',
		components: {
			'wnl-form': Form,
			'wnl-input': Input,
			'wnl-quill': Quill
		},
		data() {
			return {
				keyboard: {
					bindings: {
						tab: false,
						handleEnter: {
							key: 13,
							handler: (event) => {
								console.log('halko');
								return false;
							}
						}
					}
				},
			}
		},
		computed: {
			quillOptions() {
				return {
					theme: 'bubble',
					placeholder: 'Twoja wiadomość...',
					formats: ['bold', 'italic', 'underline', 'link', 'mention'],
					bounds: `#${this.formId}`,
				}
			},
			toolbar() {
				return [
					['bold', 'italic', 'underline', 'link'],
					[{ color: fontColors }],
					['clean'],
				]
			},
		},
		methods: {
			onInput() {
				console.log('onInput');
			}
		}
	}
</script>
