<template>
	<div>
		<div class="users-search">
			<div class="field has-addons">
				<div class="control">
					<input
						v-model="currentPhrase"
						class="input"
						placeholder="Szukaj..."
						@keyup.enter="search"
					/>
				</div>
				<div class="control">
					<a class="button is-primary" @click="search">
						Szukaj
					</a>
				</div>
			</div>
			<div v-if="availableFields.length > 1" class="search-settings__field field">
				<input
					id="allFields"
					type="checkbox"
					:checked="!searchFields.length"
					class="search-settings__field__input is-checkradio"
					@change="onSelectAll"
				>
				<label for="allFields" class="search-settings__field__label checkbox">Wszystkie Pola</label>
			</div>
			<div
				v-for="field in availableFields"
				:key="field.value"
				class="search-settings__field field"
			>
				<input
					:id="field.value"
					v-model="searchFields"
					type="checkbox"
					:value="field.value"
					class="search-settings__field__input is-checkradio"
				>
				<label :for="field.value" class="search-settings__field__label checkbox">{{field.title}}</label>
			</div>
		</div>
		<div v-if="searchPhrase" class="active-search">
			<span>Aktualne wyszukiwanie:</span>
			<span class="tag is-success">
				{{searchPhrase}}
				<button class="delete is-small" @click="clearSearch"></button>
			</span>
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
	.active-search
		margin-top: $margin-base
</style>
<script>
import { isEqual } from 'lodash';

export default {
	props: {
		/**
			 * Example:
			 * availableFields: [
			 * 	{value: 'id', title: 'ID'},
			 * 	{value: 'email', title: 'Email'},
			 * 	...
			 * ]
			 */
		availableFields: {
			type: Array,
			default: () => [],
		},
	},
	data() {
		return {
			currentPhrase: '',
			searchPhrase: '',
			searchFields: [],
		};
	},
	computed: {
		routerSearchFields() {
			const fields = this.$route.query.fields;

			if (fields && !Array.isArray(fields)) {
				return [fields];
			}

			return fields || [];
		}
	},
	methods: {
		onSelectAll() {
			this.searchFields = [];
		},
		emitSearch() {
			this.$router.push({ query: {
				...this.$route.query,
				q: this.searchPhrase !== '' ? this.searchPhrase : undefined,
				fields: this.searchFields }
			});
			this.$emit('search', {
				phrase: this.searchPhrase,
				fields: this.searchFields
			});
		},
		search() {
			this.searchPhrase = this.currentPhrase;
			this.emitSearch();
		},
		clearSearch() {
			this.searchPhrase = '';
			this.emitSearch();
		}
	},
	mounted() {
		const query = this.$route.query.q || '';

		if (query !== this.searchPhrase || !isEqual(this.searchFields, this.routerSearchFields)) {
			this.searchPhrase = this.$route.query.q;
			this.searchFields = this.routerSearchFields;
			this.emitSearch();
		}
	}
};
</script>
