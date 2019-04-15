<template>
	<form @submit.prevent="onSearch">
		<div
			v-for="(meta, contentType) in contentTypes"
			:key="contentType"
			class="field"
		>
			<label class="label">{{meta.name}}</label>
			<input
				v-model="filters[contentType]"
				class="input"
				placeholder="Wpisz id po przecinku: 36,45,..."
			/>
		</div>
		<button
			class="button submit is-primary"
			type="submit"
			:disabled="submitDisabled"
		>
			Szukaj
		</button>
	</form>
</template>

<script>
export default {
	props: {
		contentTypes: {
			type: Object,
			required: true,
		}
	},
	data() {

		const filters = Object.keys(this.contentTypes).reduce(
			(collector, contentType) => {
				collector[contentType] = '';
				return collector;
			},
			{}
		);

		return {
			filters,
		};
	},
	computed: {
		submitDisabled() {
			return !Object.values(this.filters).some(filter => filter !== '');
		}
	},
	methods: {
		onSearch() {
			this.$emit('search', this.filters);
		},
	}
};
</script>
