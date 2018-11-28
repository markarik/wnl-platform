<template>
	<div>
		<user-edit-form @success="onSuccess" @error="onError" :populate="true" :resource-url="resourceUrl" method="put"/>
	</div>
</template>

<script>
	import {mapActions} from 'vuex';
	import UserEditForm from "./UserEditForm"
	import { getApiUrl } from 'js/utils/env';


	export default {
		components: {UserEditForm},
		props: {
			user: {
				type: Object,
				required: true
			}
		},
		computed: {
			resourceUrl() {
				return getApiUrl(`users/${this.user.id}?include=roles`)
			}
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			onSuccess() {
				this.addAutoDismissableAlert({
					text: 'Użytkownik zapisany!',
					type: 'success'
				});
			},
			onError() {
				this.addAutoDismissableAlert({
					text: 'Nie udało się zapisać zmian.:(',
					type: 'error'
				});
			}
		},
	}
</script>
