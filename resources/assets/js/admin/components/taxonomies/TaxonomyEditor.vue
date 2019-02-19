<template>
	<div>
		<wnl-form
			:method="method"
			:resource-route="resourceRoute"
			:populate="isEdit"
			:hideDefaultSubmit="true"
			@formIsLoaded="onFormIsLoaded"
			@submitSuccess="onSubmitSuccess"
			name="TaxonomyEditor"
		>
			<div class="header">
				<h2 class="title is-2">
					<span v-if="isEdit">Taksonomia:
						<span v-if="name">{{name}}</span>
						<span v-else>...</span>
					</span>
					<span v-else>Dodaj taksonomię</span>
				</h2>
				<div class="field is-grouped">
					<!-- TODO PLAT-924 unblock deleting "reserved" taxonomies -->
					<button v-if="isEdit && id > 3" class="button is-danger margin right" type="button" @click="onDelete">
						<span class="icon is-small"><i class="fa fa-trash"></i></span>
						<span>Usuń</span>
					</button>
					<wnl-submit v-if="!isEdit || isEditFormVisible" class="submit"/>
					<button
						v-if="isEdit && !isEditFormVisible"
						class="button"
						@click="isEditFormVisible = true"
					>
						<span class="icon is-small"><i class="fa fa-pencil"></i></span>
						<span>Edytuj</span>
					</button>
				</div>
			</div>
			<div v-if="!isEdit || isEditFormVisible" class="editor">
				<wnl-form-text
					name="name"
					class="margin top bottom"
				>Nazwa</wnl-form-text>
				<wnl-textarea
					name="description"
					class="margin top bottom"
				>Opis</wnl-textarea>
			</div>
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
		align-items: flex-start
		display: flex
		justify-content: space-between
		margin-bottom: $margin-medium
		padding-top: $margin-small

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
	data() {
		return {
			isEditFormVisible: false,
			name: null,
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
		onFormIsLoaded({name}) {
			this.name = name;
		},
		onSubmitSuccess({id, name}) {
			this.resetTaxonomies();
			this.name = name;

			if (this.isEdit) {
				this.isEditFormVisible = false;
			} else {
				this.$router.push({name: 'taxonomy-edit', params: {id}});
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
