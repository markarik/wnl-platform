<template>
	<div>
		<h2 class="title is-3 margin bottom">Nowy Użytkownik</h2>
		<user-edit-form @success="onSuccess" @error="onError" :resource-url="resourceUrl"/>
	</div>
</template>

<script>
import {mapActions} from 'vuex';
import UserEditForm from './UserEditForm';


export default {
	components: {UserEditForm},
	computed: {
		resourceUrl() {
			return 'users';
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		onSuccess() {
			this.addAutoDismissableAlert({
				text: 'Użytkownik utworzony!',
				type: 'success'
			});
			this.$router.push({
				name: 'users'
			});
		},
		onError() {
			this.addAutoDismissableAlert({
				text: 'Nie udało się utworzyć użytkownika.:(',
				type: 'error'
			});
		}
	},
};
</script>
