<template>
	<div>
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
			<div v-for="role in availableRoles" :key="role.id" class="field">
				<input type="checkbox" :id="role.name" class="is-checkradio" v-model="selectedRoles" :value="role.id"/>
				<label class="checkbox" :for="role.name">{{role.name}}</label>
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
		props: {
			resourceUrl: {
				type: String,
				required: true
			},
			populate: {
				type: Boolean,
				default: false
			},
			method: {
				type: String,
				default: 'post'
			}
		},
		data() {
			return {
				form: new Form({
					first_name: '',
					last_name: '',
					email: '',
					password: '',
					roles: []
				}),
				availableRoles: [],
				selectedRoles: [],
				isLoading: false,
				isEdit: false
			}
		},
		computed: {
			hasChanged() {
				return !isEqual(this.form.originalData, this.form.data());
			},
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
					await this.form[this.method](this.resourceUrl)
					this.$emit('success')
				} catch (exception) {
					$wnl.logger.capture(exception)
					this.$emit('error')
				} finally {
					this.loading = false;
				}
			}
		},
		async created() {
			const response = await axios.get(getApiUrl('roles/all'))
			this.availableRoles = response.data
			if (this.populate) {
				await this.form.populate(this.resourceUrl)
				this.selectedRoles = this.form.roles
			}
		},
		watch: {
			selectedRoles() {
				this.form.roles = this.selectedRoles
			}
		}
	}
</script>
