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
				type="text"
				class="input"
				@input="onInput"
				ref=input
			>
			<wnl-autocomplete
				:items="autocompleteItems"
				:onItemChosen="insertTag"
				:isDown="true"
				:itemComponent="'wnl-tag-autocomplete-item'"
				ref="autocomplete"
			>
			</wnl-autocomplete>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	
	.tag
		color: $color-ocean-blue
		cursor: pointer
		font-size: 1rem
		height: auto
		margin: 10px 10px 10px 0
		padding: 5px 10px

		.icon
			padding: 5px
</style>

<script>
	import { formInput } from 'js/mixins/form-input'
	import Autocomplete from 'js/components/global/autocomplete'
	import _ from 'lodash'
	import { mapActions } from 'vuex'

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
		props: ['defaultTags'],
		data: function () {
			return {
				autocompleteItems: [],
				tags: []
			}
		},
		computed: {
			default() {
				return ''
			},
		},
		methods: {
			...mapActions(['requestTagsAutocomplete']),
			onKeyDown() {
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
				this.tags.push(tag);
				this.autocompleteItems = []
				this.$refs.input.value = ''
			},

			removeTag(tag) {
				this.tags = _.filter(
					this.tags,
					foundTag => tag.id !== foundTag.id
				)
			},

			onInput(evt) {
				const data = { name: evt.target.value }

				if (!data.name) return

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
		watch: {
			defaultTags() {
				this.tags = this.defaultTags
			}
		}
	}
</script>
