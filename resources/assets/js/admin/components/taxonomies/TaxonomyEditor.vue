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
		<wnl-taxonomy-terms-editor :taxonomyId="id" v-if="isEdit" />
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
import WnlTaxonomyTermsEditor from 'js/admin/components/taxonomies/TaxonomyTermsEditor';
import {getApiUrl} from 'js/utils/env';
import {ALERT_TYPES} from 'js/consts/alert';

export default {
	props: {
		id: {
			type: [String, Number],
			required: true,
		},
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
	},
	components: {
		WnlFormText,
		WnlForm,
		WnlSubmit,
		WnlTextarea,
		WnlTaxonomyTermsEditor,
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('taxonomies', ['resetTaxonomies']),
		onSubmitSuccess(data) {
			this.resetTaxonomies();
			if (!this.isEdit) {
				this.$router.push({ name: 'taxonomy-edit', params: { id: data.id } });
			}
		},
		async onDelete() {
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
				this.resetTaxonomies();
				this.addAutoDismissableAlert({
					text: 'Taksonomia została usunięta',
					type: ALERT_TYPES.SUCCESS
				});
				this.$router.push({ name: 'taxonomies' });
			} catch (error) {
				this.addAutoDismissableAlert({
					text: 'Coś poszło nie tak. Spróbuj ponownie.',
					type: ALERT_TYPES.ERROR
				});
			}
		},
	},
};
</script>
