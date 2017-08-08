<template>
	<div class="quill-container">
		<wnl-autocomplete :items="autocompleteItems" :onItemChosen="insertMention"></wnl-autocomplete>

		<div ref="quill">
			<slot></slot>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.quill-container
		position: relative

	.quill-mention
		background: #eee
		border: 1px solid #ddd
		padding: 3px
</style>

<script>
	import _ from 'lodash'
	import Quill from 'quill'
	import { set } from 'vue'
	import { mapActions } from 'vuex'

	import { formInput } from 'js/mixins/form-input'
	import { fontColors } from 'js/utils/colors'
	import { mentionBlot } from 'js/classes/mentionblot'
	import Autocomplete from 'js/components/global/autocomplete'

	const defaults = {
		theme: 'snow',
		modules: {},
		placeholder: 'Pisz tutaj...',
	}

	const firstAndLastNameMatcher = /@([\w\da-pr-uwy-zA-PR-UWY-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+) {1}([\w\da-pr-uwy-zA-PR-UWY-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+)$/i;
	const firstNameMatcher = /@([\w\da-pr-uwy-zA-PR-UWY-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+)$/i;

	export default {
		name: 'Quill',
		mixins: [formInput],
		components: {
			'wnl-autocomplete': Autocomplete
		},
		props: {
			options: {
				type: Object,
				default: () => ({}),
			},
			toolbar: {
				default() {
					return [
						[{ 'header': [false, 1, 2, 3] }],
						['bold', 'italic', 'underline', 'link'],
						[{ color: fontColors }],
						['clean'],
						[{ list: 'ordered' }, { list: 'bullet' }, { 'indent': '-1'}, { 'indent': '+1' }],
					]
				}
			},
			autofocus: {
				type: Boolean,
				default: true,
			},
			name: String,
			theme: String,
			keyboard: {
				type: Object,
				default: () => {}
			}
		},
		data () {
			return {
				focused: this.autofocus,
				quill: null,
				editor: null,
				autocompleteItems: [],
				lastAutocompleteQuery: null,
				lastRange: null
			}
		},
		computed: {
			default() {
				return ''
			},
			quillOptions() {
				const options = {
					...defaults,
					...this.options,
					...{
						modules: {
							keyboard: this.keyboard,
							toolbar: this.toolbar
						}
					}
				}

				return options
			},
		},
		methods: {
			...mapActions(['requestUsersAutocomplete']),
			onTextChange(delta, oldDelta, source) {
				this.setValue(this.editor.innerHTML)
				this.$emit('input', this.editor.innerHTML)

				if (source !== Quill.sources.USER) {
					return;
				}

				const currentMention = this.getCurrentMention()

				if (currentMention) {
					this.requestUsersAutocomplete(currentMention).then(this.onMentionsFetched.bind(this))
				}
			},
			onSelectionChange(range, oldRange, source) {

			},
			insertMention(data) {
				this.autocompleteItems = []
				const lastMentionQueryLength = this.lastAutocompleteQuery.length
				const mentionStartIndex = this.lastRange.index - lastMentionQueryLength

				this.quill.deleteText(mentionStartIndex, lastMentionQueryLength, Quill.sources.API)

				const range = this.lastRange; // cursor position

			  	if (!range || range.length != 0) return
			  	const position = range.index - lastMentionQueryLength

			  	this.quill.insertEmbed(position, 'mention', {name: data.full_name, id:"123"}, Quill.sources.API);
			  	this.quill.insertText(position + 1, ' ', Quill.sources.API);
			  	this.quill.setSelection(position + 2, Quill.sources.API);
			},
			getCurrentMention() {
				this.lastRange = this.quill.getSelection()
			    if (!this.lastRange) {
					return
				}

			    var cursor = this.lastRange.index,
			        contents = this.quill.getText(0, cursor)

				let currentMentionMatch = null

			    const bothNamesMatch = firstAndLastNameMatcher.exec(contents)

				if (bothNamesMatch) {
					console.log('both names matched', bothNamesMatch)

					currentMentionMatch = {
						firstName: bothNamesMatch[1],
						lastName: bothNamesMatch[2],
					}
					this.lastAutocompleteQuery = bothNamesMatch[0]
				} else {
					const oneNameMatch = firstNameMatcher.exec(contents)

					if (oneNameMatch) {
						console.log('one name matched', oneNameMatch)

						currentMentionMatch = {
							firstName: oneNameMatch[1],
							lastName: ''
						}
						this.lastAutocompleteQuery = oneNameMatch[0]
					}
				}

				if (!currentMentionMatch) {
					this.autocompleteItems = []
				}

				return currentMentionMatch
			},

			onMentionsFetched(response) {
				this.autocompleteItems = response.data
			}
		},
		mounted () {
			this.quill = new Quill(this.$refs.quill, this.quillOptions)
			this.QuillEmbed = Quill.import('blots/embed');
			this.editor = this.$refs.quill.firstElementChild
			this.quill.on('text-change', this.onTextChange)
			this.quill.on('selection-change', this.onSelectionChange)
		},
		watch: {
			focused (val) {
				this.editor[val ? 'focus' : 'blur']()
			},
			inputValue (newValue) {
				if (newValue !== this.editor.innerHTML) {
					this.editor.innerHTML = newValue
				}
			}
		}
	}
</script>
