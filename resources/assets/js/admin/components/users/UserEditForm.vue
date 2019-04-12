<template>
	<div>
		<wnl-form
			:method="method"
			:resource-route="resourceUrl"
			:populate="populate"
			name="UserEditForm"
			@submitSuccess="$emit('success')"
		>
			<wnl-form-text
				name="first_name"
				class="margin top"
			>Imię</wnl-form-text>
			<wnl-form-text
				name="last_name"
				class="margin top"
			>Nazwisko</wnl-form-text>
			<wnl-form-text
				name="email"
				class="margin top"
			>E-mail</wnl-form-text>
			<wnl-form-text
				name="password"
				class="margin top"
			>Hasło</wnl-form-text>
			<h3 class="title is-5 margin vertical">Dodaj Rolę</h3>
			<wnl-form-checkbox
				v-for="role in availableRoles"
				:key="role.id"
				:checkbox-id="role.name"
				:checkbox-value="role.id"
				name="roles"
			>{{role.name}}</wnl-form-checkbox>
		</wnl-form>
	</div>
</template>

<script>
import axios from 'axios';
import { getApiUrl } from 'js/utils/env';
import { Checkbox as WnlFormCheckbox, Form as WnlForm, Text as WnlFormText } from 'js/components/global/form';

export default {
	components: { WnlForm, WnlFormText, WnlFormCheckbox },
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
			availableRoles: [],
		};
	},
	async created() {
		const response = await axios.get(getApiUrl('roles/all'));
		this.availableRoles = response.data;
	},
};
</script>
