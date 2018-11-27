<template>
	<div class="users-search">
		<div class="field has-addons">
			<div class="control">
				<input class="input" v-model="searchPhrase" placeholder="szukaj użytkownika..." @keyup.enter="search"/>
			</div>
			<div class="control">
				<a class="button is-primary" @click="search">
					Szukaj
				</a>
			</div>
		</div>
		<div class="search-settings__field field">
			<input
					type="checkbox" id="allFields" :checked="!searchFields.length"
					@change="onSelectAll" class="search-settings__field__input is-checkradio"
			>
			<label for="allFields" class="search-settings__field__label checkbox">Wszystkie Pola</label>
		</div>
		<div v-for="field in availableFields" :key="field.value" class="search-settings__field field">
			<input
					type="checkbox" v-model="searchFields"
					:id="field.value" :value="field.value" class="search-settings__field__input is-checkradio"
			>
			<label :for="field.value" class="search-settings__field__label checkbox">{{field.title}}</label>
		</div>

	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'
	.users-search
		display: flex
		flex-direction: row

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
