<template>
	<div class="quill-container" @keydown="allowMentions && onKeyDown($event)">
		<wnl-autocomplete-list
			:items="items"
			:active-index="activeIndex"
			@change="insertMention"
		>
			<wnl-user-autocomplete-item slot-scope="slotProps" :item="slotProps.item" />
		</wnl-autocomplete-list>
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
import Quill from 'quill';
import { mapActions } from 'vuex';
import { cloneDeep } from 'lodash';

import { formInput } from 'js/mixins/form-input';
import { fontColors } from 'js/utils/colors';
import WnlAutocompleteList from 'js/components/global/AutocompleteList';
import WnlUserAutocompleteItem from 'js/components/global/UserAutocompleteItem';
import WnlAutocompleteKeyboardNavigation from 'js/mixins/autocomplete-keyboard-navigation';

const defaults = {
	theme: 'snow',
	modules: {},
	placeholder: 'Pisz tutaj...',
};

const firstAndLastNameMatcher = /@([\w\da-pr-uwy-zA-PR-UWY-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+) {1}([\w\da-pr-uwy-zA-PR-UWY-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+)$/i;
const firstNameMatcher = /@([\w\da-pr-uwy-zA-PR-UWY-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+)$/i;
const autocompleteChar = '@';

export default {
	name: 'Quill',
	mixins: [formInput, WnlAutocompleteKeyboardNavigation],
	components: {
		WnlAutocompleteList,
		WnlUserAutocompleteItem
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
					[{ list: 'ordered' }, { list: 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }],
				];
			}
		},
		value: {
			type: String,
			default: ''
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
			items: [],
			lastAutocompleteQuery: null,
			lastRange: null
		};
	},
	computed: {
		default() {
			return '';
		},
		quillOptions() {
			const keyboardModule = cloneDeep(this.keyboard);

			if (keyboardModule && keyboardModule.bindings && keyboardModule.bindings.handleEnter) {
				keyboardModule.bindings.handleEnter.handler = () => {
					if (this.items.length) {
						// Prevent enter handler when autocomplete is open
						return;
					}

					this.keyboard.bindings.handleEnter.handler();
				};
			}


			return {
				...defaults,
				...this.options,
				...{
					modules: {
						keyboard: keyboardModule,
						toolbar: this.toolbar
					}
				}
			};
		},
	},
	methods: {
		...mapActions(['requestUsersAutocomplete']),
		onTextChange(delta, oldDelta, source) {
			this.setValue(this.editor.innerHTML);
			this.$emit('input', this.editor.innerHTML);

			if (source !== Quill.sources.USER || !this.allowMentions) {
				return;
			}

			const currentMention = this.getCurrentMention();

			if (currentMention) {

				this.requestUsersAutocomplete(currentMention).then(this.onMentionsFetched.bind(this));
			}
		},
		insertMention(data) {
			this.items = [];
			const lastMentionQueryLength = this.lastAutocompleteQuery.length;
			const mentionStartIndex = this.lastRange.index - lastMentionQueryLength;

			this.quill.deleteText(mentionStartIndex, lastMentionQueryLength, Quill.sources.API);

			// cursor position
			const range = this.lastRange;

			if (!range || range.length != 0) return;
			const position = range.index - lastMentionQueryLength;

			this.quill.insertEmbed(position, 'mention', {
				name: `${autocompleteChar}${data.full_name}`,
				id: data.user_id
			}, Quill.sources.API);
			this.quill.insertText(position + 1, ' ', Quill.sources.API);
			this.quill.setSelection(position + 2, Quill.sources.API);
		},
		getCurrentMention() {
			this.lastRange = this.quill.getSelection();

			if (!this.lastRange) {
				return;
			}

			var cursor = this.lastRange.index,
				contents = this.quill.getText(0, cursor);

			let currentMentionMatch = null;

			const bothNamesMatch = firstAndLastNameMatcher.exec(contents);

			if (bothNamesMatch) {
				currentMentionMatch = {
					firstName: bothNamesMatch[1],
					lastName: bothNamesMatch[2],
				};
				this.lastAutocompleteQuery = bothNamesMatch[0];
			} else {
				const oneNameMatch = firstNameMatcher.exec(contents);

				if (oneNameMatch) {
					currentMentionMatch = {
						firstName: oneNameMatch[1],
						lastName: ''
					};
					this.lastAutocompleteQuery = oneNameMatch[0];
				}
			}

			if (!currentMentionMatch) {
				this.items = [];
			}

			return currentMentionMatch;
		},

		onMentionsFetched(response) {
			this.items = response.data;
		},

		onEnter(evt) {
			const activeIndex = this.activeIndex;

			if (activeIndex < 0) return;

			this.insertMention(this.items[activeIndex]);

			this.killEvent(evt);
			return false;
		},

		onEsc() {
			this.items = [];
			this.editor.focus();
		},

		clickHandler({ target }) {
			if (this.$el !== target && !this.$el.contains(target)) {
				this.items = [];
			}
		},
		// DON'T REMOVE ME, I'M USED VIA $REFS
		clear() {
			this.items = [];
			this.quill.deleteText(0, this.editor.innerHTML.length);
		}
	},
	mounted () {
		this.quill = new Quill(this.$refs.quill, this.quillOptions);
		this.QuillEmbed = Quill.import('blots/embed');
		this.editor = this.$refs.quill.firstElementChild;
		this.$nextTick(() => {
			this.editor.innerHTML = this.value;
			this.quill.on('text-change', this.onTextChange);
			document.addEventListener('click', this.clickHandler);
		});
	},
	beforeDestroy() {
		document.removeEventListener('click', this.clickHandler);
	},
	watch: {
		focused (val) {
			this.editor[val ? 'focus' : 'blur']();
		},
		inputValue (newValue) {
			if (newValue !== this.editor.innerHTML) {
				this.editor.innerHTML = newValue;
			}
		}
	}
};
</script>
