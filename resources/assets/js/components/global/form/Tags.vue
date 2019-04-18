<template>
	<div class="field">
		<div class="control tags-control">
			<div
				v-for="tag in tags"
				:key="tag.id"
				class="tag"
				@click="removeTag(tag)"
			>
				{{tag.name}}
				<span class="icon is-small">
					<i class="fa fa-times" />
				</span>
			</div>
			<wnl-autocomplete
				v-model="tagInput"
				:items="autocompleteItems"
				placeholder="Dodaj tag"
				@change="insertTag"
			>
				<template slot-scope="slotProps">
					<wnl-tag-autocomplete-item :item="slotProps.item" />
				</template>
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
import Autocomplete from 'js/components/global/Autocomplete';
import WnlTagAutocompleteItem from 'js/components/global/TagAutocompleteItem';
import _ from 'lodash';
import { mapActions } from 'vuex';

export default {
	name: 'Tags',
	components: {
		'wnl-autocomplete': Autocomplete,
		WnlTagAutocompleteItem,
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
		};
	},
	computed: {
		default() {
			return '';
		}
	},
	watch: {
		defaultTags() {
			this.tags = this.defaultTags.slice();
		},
		tagInput() {
			const name = this.tagInput;
			const data = { name, tags: this.tags };

			if (!name) {
				this.autocompleteItems = [];
				return;
			}

			this.requestTagsAutocomplete(data).then(
				data => {
					this.autocompleteItems = data.data;
				}
			);
		}
	},
	created() {
		this.tags = this.defaultTags.slice();
	},
	methods: {
		...mapActions(['requestTagsAutocomplete']),

		insertTag(tag) {
			if (_.map(this.tags, (tag) => tag.id).indexOf(tag.id) === -1) {
				this.tags.push(tag);
			}

			this.autocompleteItems = [];
			this.tagInput = '';
			this.$emit('insertTag', tag);
			this.$emit('tagsChanged', this.tags);
		},

		removeTag(tag) {
			this.tags = _.filter(
				this.tags,
				foundTag => tag.id !== foundTag.id
			);
			this.$emit('tagsChanged', this.tags);
		},

		haveTagsChanged() {
			if (this.tags.length !== this.defaultTags.length) return true;

			return !!this.tags.some(tag => !_.find(this.defaultTags, defTag => defTag.id === tag.id));
		}
	},
};
</script>
