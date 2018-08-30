<template>
	<div class="quill-container" @keydown="onKeyDown">
		<wnl-autocomplete
			:items="autocompleteItems"
			:onItemChosen="insertMention"
			:itemComponent="'wnl-user-autocomplete-item'"
			ref="autocomplete"
		>
		</wnl-autocomplete>
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
		color: $mention-text-color
</style>

<script>
	import _ from 'lodash'
	import Quill from 'quill'
	import { set } from 'vue'
	import { mapActions } from 'vuex'

	import { formInput } from 'js/mixins/form-input'
	import { fontColors } from 'js/utils/colors'
	import { mentionBlot } from 'js/classes/mentionblot'
	import Autocomplete from 'js/components/global/Autocomplete'

	const defaults = {
		theme: 'snow',
		modules: {},
		placeholder: 'Pisz tutaj...',
	}

	const firstAndLastNameMatcher = /@([\w\da-pr-uwy-zA-PR-UWY-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+) {1}([\w\da-pr-uwy-zA-PR-UWY-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+)$/i
	const firstNameMatcher = /@([\w\da-pr-uwy-zA-PR-UWY-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+)$/i
	const autocompleteChar = '@'
	const keys = {
		enter: 13,
		esc: 27,
		arrowUp: 38,
		arrowDown: 40
	}

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
			},
			allowMentions: {
				type: Boolean,
				default: false
			}
		},
		data () {
			return {
				focused: this.autofocus,
				quill: null,
				autocomplete: null,
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

				if (source !== Quill.sources.USER || !this.allowMentions) {
					return
				}

				const currentMention = this.getCurrentMention()

				if (currentMention) {

					this.requestUsersAutocomplete(currentMention).then(this.onMentionsFetched.bind(this))
				}
			},
			insertMention(data) {
				this.autocompleteItems = []
				const lastMentionQueryLength = this.lastAutocompleteQuery.length
				const mentionStartIndex = this.lastRange.index - lastMentionQueryLength

				this.quill.deleteText(mentionStartIndex, lastMentionQueryLength, Quill.sources.API)

				// cursor position
				const range = this.lastRange

					if (!range || range.length != 0) return
					const position = range.index - lastMentionQueryLength

				const name = data.display_name ? data.display_name : data.full_name

					this.quill.insertEmbed(position, 'mention', {
					name: `${autocompleteChar}${name}`,
					id: data.user_id
				}, Quill.sources.API)
					this.quill.insertText(position + 1, ' ', Quill.sources.API)
					this.quill.setSelection(position + 2, Quill.sources.API)
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
					currentMentionMatch = {
						firstName: bothNamesMatch[1],
						lastName: bothNamesMatch[2],
					}
					this.lastAutocompleteQuery = bothNamesMatch[0]
				} else {
					const oneNameMatch = firstNameMatcher.exec(contents)

					if (oneNameMatch) {
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
			},

			onKeyDown(evt) {
				const { enter, esc, arrowUp, arrowDown } = keys

				if (this.autocompleteItems.length === 0 || !this.allowMentions) {
					return
				}

				if (evt.keyCode === esc) {
					this.onEsc(evt)
					return
				}

				if ([enter, arrowUp, arrowDown].indexOf(evt.keyCode) === -1) {
					return
				}

				this.$refs.autocomplete.onKeyDown(evt)
				this.killEvent(evt)

				//for some of the old browsers, returning false is the true way to kill propagation
				return false
			},

			onEsc(evt) {
				this.autocompleteItems = []
				this.editor.focus()
			},

			killEvent(evt) {
				evt.preventDefault()
				evt.stopPropagation()
			},

			clickHandler({ target }) {
				if (this.$el !== target && !this.$el.contains(target)) {
					this.autocompleteItems = []
				}
			},

			clear() {
				this.autocompleteItems = []
				this.quill.deleteText(0, this.editor.innerHTML.length)
			}
		},
		mounted () {
			this.quill = new Quill(this.$refs.quill, this.quillOptions)
			this.QuillEmbed = Quill.import('blots/embed')
			this.editor = this.$refs.quill.firstElementChild
			this.quill.on('text-change', this.onTextChange)
			document.addEventListener('click', this.clickHandler)
		},
		beforeDestroy() {
			document.removeEventListener('click', this.clickHandler)
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
