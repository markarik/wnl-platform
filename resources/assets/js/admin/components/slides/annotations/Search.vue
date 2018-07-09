<template>
	<div>
		<div class="field has-addons">
			<div class="control">
				<input class="input" v-model="searchPhrase" placeholder="szukaj przypisu..."/>
			</div>
			<div class="control">
				<a class="button is-primary" @click="search">
					Szukaj
				</a>
			</div>
		</div>
		<p class="title is-6 search-settings-title">Szukaj w:</p>
		<div class="search-settings">
			<div class="search-settings__field field">
				<input
						type="checkbox" id="all" :checked="!searchFields.length"
						@change="onSelectAll" class="search-settings__field__input is-checkradio"
				>
				<label for="all" class="search-settings__field__label checkbox">Wszystkie Pola</label>
			</div>
			<div v-for="field in availableFields" :key="field.value" class="search-settings__field field">
					<input
							type="checkbox" v-model="searchFields"
							:id="field.value" :value="field.value" class="search-settings__field__input is-checkradio"
					>
				<label :for="field.value" class="search-settings__field__label checkbox">{{field.title}}</label>
			</div>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.search-settings-title
			margin-bottom: $margin-small
	.search-settings
		display: flex
		&__field
			padding: 5px 10px
			&__input
				border-radius: 50%
</style>
<script>
	export default {
		data() {
			return {
				searchPhrase: '',
				searchFields: [],
				availableFields: [
					{value: 'id', title: 'ID'},
					{value: 'title', title: 'Tytuł'},
					{value: 'description', title: 'Treść'},
					{value: 'tags.name', title: 'Nazwa Taga'},
				]
			}
		},
		methods: {
			onSelectAll() {
				this.searchFields = [];
			},
			search() {
				this.$emit('search', {
					phrase: this.searchPhrase,
					fields: this.searchFields
				})
			}
		}
	}
</script>
