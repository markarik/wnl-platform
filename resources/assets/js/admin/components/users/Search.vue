<template>
	<div>
		<div class="field has-addons">
			<div class="control">
				<input class="input" v-model="searchPhrase" placeholder="Szukaj..." @keyup="search"/>
			</div>
			<div class="search-settings__field field">
				<input
						type="checkbox" id="all" :checked="!searchFields.length"
						@change="onSelectAll" class="search-settings__field__input is-checkradio"
				>
				<label for="all" class="search-settings__field__label checkbox">Wszystkie Pola</label>
			</div>
			<div v-for="field in availableFields" :key="field.value" class="search-settings__field field">
				<input
						type="checkbox" v-model="searchFields" @change="search"
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
			padding: 10px 10px
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
					{value: 'eamil', title: 'Email'},
					{value: 'full_name', title: 'Imię i nazwisko'},
				]
			}
		},
		methods: {
			onSelectAll() {
				this.searchFields = [];
				this.search();
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
