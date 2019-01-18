<template>
	<div>
		<wnl-form
			:method="method"
			:resource-route="resourceRoute"
			:populate="isEdit"
			:hideDefaultSubmit="true"
			@submitSuccess="onSubmitSuccess"
			name="TaxonomyEditor"
			class="editor"
		>
			<div class="header">
				<h2 class="title is-2">Edycja taksonomii <span v-if="isEdit">(Id: {{id}})</span></h2>
				<div class="field is-grouped">
					<!-- TODO PLAT-924 unblock deleting "reserved" taxonomies -->
					<button v-if="isEdit && id > 3" class="button is-danger margin right" type="button" @click="onDelete">
						<span class="icon is-small"><i class="fa fa-trash"></i></span>
						<span>Usuń</span>
					</button>
					<wnl-submit class="submit"/>
				</div>
			</div>
			<wnl-form-text
				name="name"
				class="margin top bottom"
			>Nazwa</wnl-form-text>
			<wnl-textarea
				name="description"
				class="margin top bottom"
			>Opis</wnl-textarea>
		</wnl-form>
		<h3 class="title is-3">Pojęcia</h3>
		<ul class="content">
			<wnl-taxonomy-term-item
				v-for="term in rootTerms"
				:term="term"
				:included="included"
				:terms="terms"
				:key="term.id"
			/>
		</ul>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.editor
		max-width: 800px

	.header
		+small-shadow-bottom()
		align-items: flex-start
		display: flex
		justify-content: space-between
		background: $color-white
		margin-bottom: $margin-medium
		padding-top: $margin-small
		position: sticky
		top: -$margin-big
		z-index: 101

		.submit
			width: auto
</style>

<script>
import {mapActions} from 'vuex';

import {Form as WnlForm, Text as WnlFormText, Submit as WnlSubmit, Textarea as WnlTextarea} from 'js/components/global/form';
import WnlTaxonomyTermItem from 'js/admin/components/taxonomies/TaxonomyTermItem';
import {getApiUrl} from 'js/utils/env';
import {ALERT_TYPES} from '../../../consts/alert';

export default {
	props: {
		id: {
			type: String|Number,
			required: true,
		},
	},
	data() {
		return {
			terms: [],
			included: {},
		};
	},
	computed: {
		isEdit() {
			return this.id !== 'new';
		},
		method() {
			return this.isEdit ? 'put' : 'post';
		},
		resourceRoute() {
			return this.isEdit ? `taxonomies/${this.id}` : 'taxonomies';
		},
		rootTerms() {
			return this.terms.filter(term => term.parent_id === null);
		}
	},
	components: {
		WnlFormText,
		WnlForm,
		WnlSubmit,
		WnlTextarea,
		WnlTaxonomyTermItem
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		onSubmitSuccess(data) {
			if (!this.isEdit) {
				this.$router.push({ name: 'taxonomy-edit', params: { id: data.id } });
			}
		},
		async onDelete() {
			// TODO protect id=1,2,3 from being deleted
			try {
				await this.$swal({
					type: 'warning',
					text: 'Czy jesteś pewien, że chcesz usunąć tę taksonomię wraz z wszystkimi powiązanymi pojęciami?',
					showCancelButton: true,
					confirmButtonText: 'Tak',
					cancelButtonText: 'Nie, jeszcze nad tym pomyślę',
					confirmButtonColor: '#e53d2c',
				});
			} catch (error) {
				// Handle no confirmation
				return;
			}

			try {
				await axios.delete(getApiUrl(this.resourceRoute));
				this.addAutoDismissableAlert({
					text: 'Taksonomia została usunięta',
					type: ALERT_TYPES.SUCCESS
				});
				this.$router.push({ name: 'taxonomies' });
			} catch (error) {
				this.addAutoDismissableAlert({
					text: 'Coś poszło nie tak. Spróbuj ponownie, a jak nie zadziała to daj znać nerdom',
					type: ALERT_TYPES.ERROR
				});
			}



		},
	},
	async mounted() {
		if (this.id) {
			try {
				const response = await axios.get(getApiUrl(`taxonomy_terms/byTaxonomy/${this.id}?include=tags,taxonomies`));
				const {data: {included, ...terms}} = response;
				this.terms = Object.values(terms);
				this.included = included;
			} catch (error) {
				this.addAutoDismissableAlert({
					text: 'Coś poszło nie tak przy pobieraniu struktury Taksonomii',
					type: ALERT_TYPES.ERROR
				});
			}

		}
	},
};
</script>
