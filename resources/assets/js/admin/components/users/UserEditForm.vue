<template>
	<div>
		<h2 class="title is-3 margin bottom">Nowy Użytkownik</h2>
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
			<h3 class="title is-5 margin vertical">Dodaj Rolę</h3>
			<div v-for="role in roles" :key="role.id" class="field">
				<input type="checkbox" :id="role.id" class="is-checkradio" v-model="selectedRoles" :value="role.id"/>
				<label class="checkbox" :for="role.id">{{role.name}}</label>
			</div>
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
					password: '',
					roles: []
				}),
				roles: [],
				selectedRoles: [],
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
					this.form.roles = this.selectedRoles
					await this.form[this.isEdit ? 'put' : 'post'](this.apiUrl)
					this.loading = false;
					this.addAutoDismissableAlert({
						text: 'Użytkownik utworzony!',
						type: 'success'
					});
					this.form.originalData = this.form.data()
					this.$router.push({
						name: 'users'
					})
				} catch (exception) {
					this.loading = false;
					this.addAutoDismissableAlert({
						text: 'Nie udało się utworzyć użytkownika.:(',
						type: 'error'
					});
					$wnl.logger.capture(exception)
				}
			}
		},
		async created() {
			const response = await axios.get(getApiUrl('roles/all'));
			this.roles = response.data;
		},
		watch: {
			selectedRoles() {
				this.form.roles = this.selectedRoles;
			}
		}
	}
</script>
