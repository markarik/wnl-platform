<template>
	<div>
		<wnl-input>
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
		</wnl-input>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
</style>

<script>
	import Input from 'js/admin/components/forms/Input'
	import Quill from 'js/components/global/form/Quill'

	export default {
		name: 'Users',
		components: {
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
