<template>
	<div class="field">
		<div class="control tags-control">
			<div class="tag" v-for="tag in tags" :key="tag.id" @click="removeTag(tag)">
				{{tag.name}}
				<span class="icon is-small">
					<i class="fa fa-times"></i>
				</span>
			</div>
			<input
				v-model="tagInput"
				class="input"
				type="text"
				placeholder="Dodaj tag"
				ref="input"
				@input="onInput"
				@keydown="onKeyDown"
			>
			<wnl-autocomplete
				:items="autocompleteItems"
				:onItemChosen="insertTag"
				:itemComponent="'wnl-tag-autocomplete-item'"
				ref="autocomplete"
			>
			</wnl-autocomplete>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.tags-control
		margin-bottom: 5px
	.tag
		color: $color-ocean-blue
		cursor: pointer
		font-size: 1rem
		height: auto
		margin: 0 10px 10px 0
		padding: 5px 10px

		.icon
			padding: 5px

	.tags-control
		.autocomplete-box
			box-shadow: 0px -5px 24px -5px rgba(0,0,0,0.42);
			bottom: 32px
</style>

<script>
	import Autocomplete from 'js/components/global/Autocomplete'
	import _ from 'lodash'
	import {mapActions} from 'vuex'

	const keys = {
		enter: 13,
		esc: 27,
		arrowUp: 38,
		arrowDown: 40
	}

	export default {
		name: 'Tags',
		components: {
			'wnl-autocomplete': Autocomplete
		},
		props: {
			defaultTags: {
				type: Array,
				default: () => ([])
			}
		},
		data: function () {
			return {
				autocompleteItems: [],
				tags: [],
				tagInput: '',
			}
		},
		computed: {
			default() {
				return ''
			}
		},
		methods: {
			...mapActions(['requestTagsAutocomplete']),
			onKeyDown(evt) {
				const { enter, esc, arrowUp, arrowDown } = keys

				if (this.autocompleteItems.length === 0) {
					return
				}

				if (evt.keyCode === esc) {
					this.onEsc(evt)
					return
				}

				if ([enter, arrowUp, arrowDown].indexOf(evt.keyCode) === -1) {
					return
				}

				if (evt.keyCode === enter && !this.$refs.autocomplete.hasItems) {
					return
				}

				this.$refs.autocomplete.onKeyDown(evt)
				this.killEvent(evt)

				//for some of the old browsers, returning false is the true way to kill propagation
				return false
			},

			onEsc(evt) {
				this.autocompleteItems = []
			},

			killEvent(evt) {
				evt.preventDefault()
				evt.stopPropagation()
			},

			insertTag(tag) {
				if (_.map(this.tags, (tag) => tag.id).indexOf(tag.id) === -1) {
					this.tags.push(tag)
				}

				this.autocompleteItems = []
				this.tagInput = ''
				this.$refs.input.focus()
			},

			removeTag(tag) {
				this.tags = _.filter(
					this.tags,
					foundTag => tag.id !== foundTag.id
				)
			},

			onInput(evt) {
				const name = this.tagInput
				const data = { name, tags: this.tags }

				if (!name) {
					this.autocompleteItems = []
					return
				}

				this.requestTagsAutocomplete(data).then(
					data => {
						this.autocompleteItems = data.data
					}
				)
			},
			haveTagsChanged() {
				if (this.tags.length !== this.defaultTags.length) return true

				return !!this.tags.some(tag => !_.find(this.defaultTags, defTag => defTag.id === tag.id))
			}
		},
		created() {
			this.tags = this.defaultTags.slice();
		},
		watch: {
			defaultTags() {
				this.tags = this.defaultTags.slice()
			}
		}
	}
</script>
