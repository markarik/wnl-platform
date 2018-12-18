<template>
	<button
		class="button is-danger margin right"
		type="button"
		@click="onClick"
		:disabled="!isDeleteAllowed"
	>Usuń</button>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
</style>

<script>
import axios from 'axios';
import {mapActions} from 'vuex';
import {getApiUrl} from 'js/utils/env';

export default {
	props: ['id', 'isDeleteAllowed'],
	computed: {},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		onClick() {
			axios.delete(getApiUrl(`tags/${this.id}`))
				.catch(({response: {data: {message = 'Usuwanie taga nie powiodło się.'}}}) => {
					this.addAutoDismissableAlert({
						text: message,
						type: 'error',
					});
				})
				.then(() => {
					this.addAutoDismissableAlert({
						text: 'Tag został usunięty',
						type: 'success',
					});

					// TODO uncomment when code from PLAT-826 is available
					// this.$router.push({ name: 'tags' });
				});
		}
	}
};
</script>
