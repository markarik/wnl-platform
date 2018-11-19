<template>
	<div>
		<span class="title is-5 margin bottom">Dodaj Użytkownika</span>
		<form @submit.prevent="onSubmit">
			<wnl-form-input
				name="first_name"
				:form="form"
				v-model="form.first_name"
				class="margin top"
			>
				Imię
			</wnl-form-input>
			<wnl-form-input
				name="last_name"
				:form="form"
				v-model="form.last_name"
				class="margin top"
			>
				Nazwisko
			</wnl-form-input>
			<wnl-form-input
				name="email"
				:form="form"
				v-model="form.email"
				class="margin top"
			>
				E-mail
			</wnl-form-input>
			<wnl-form-input
					name="password"
					:form="form"
					v-model="form.password"
					class="margin top"
			>
				Hasło
			</wnl-form-input>
			<button class="button is-small is-success margin top"
				:class="{'is-loading': isLoading}"
				:disabled="!hasChanged"
				type="submit"
			>
				<span class="margin right">Zapisz</span>
				<span class="icon is-small">
					<i class="fa fa-save"></i>
				</span>
			</button>
		</form>
	</div>
</template>

<script>
	import Form from 'js/classes/forms/Form'
	import {getApiUrl} from 'js/utils/env'
	import WnlFormInput from "js/admin/components/forms/Input";
	import {isEqual} from 'lodash';
	import {mapActions} from 'vuex';


	export default {
		components: {WnlFormInput},
		data() {
			return {
				form: new Form({
					first_name: '',
					last_name: '',
					email: '',
					password: ''
				}),
				isLoading: false,
				isEdit: false
			}
		},
		computed: {
			hasChanged() {
				return !isEqual(this.form.originalData, this.form.data());
			},
			apiUrl() {
				return getApiUrl('users')
			}
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			async onSubmit() {
				if (!this.hasChanged) {
					return false;
				}

				this.loading = true;
				try {
					await this.form[this.isEdit ? 'put' : 'post'](this.apiUrl)
					this.loading = false;
					this.addAutoDismissableAlert({
						text: 'Użytkownik utworzony!',
						type: 'success'
					});
					this.form.originalData = this.form.data()
				} catch (exception) {
					this.loading = false;
					this.addAutoDismissableAlert({
						text: 'Nie udało się utworzyć użytkownika.:(',
						type: 'error'
					});
					console.log(exception);
					$wnl.logger.capture(exception)
				}
			}
		}
	}
</script>
