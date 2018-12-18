<template>
	<div>
		<button
			class="button is-danger margin right"
			type="button"
			@click="onClick"
			:disabled="!isDeleteAllowed"
		>Usuń</button>
		<wnl-modal :isModalVisible="isModalVisible" @closeModal="onCloseModal" v-if="isModalVisible">
			Jest {{taggablesCount}} taggables!
		</wnl-modal>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
</style>

<script>
import axios from 'axios';
import {mapActions} from 'vuex';
import {getApiUrl} from 'js/utils/env';
import Modal from 'js/components/global/Modal';

export default {
	name: 'TagDelete',
	components: {
		'wnl-modal': Modal,
	},
	props: ['id', 'isDeleteAllowed', 'taggablesCount'],
	data: () => {
		return {
			isModalVisible: false,
		};
	},
	computed: {},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		delete: function () {
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

					this.$emit('tagDeleted');
				});
		},
		showTaggablesMoveModal() {
			this.isModalVisible = true;
		},
		onClick() {
			if (this.taggablesCount > 0) {
				this.showTaggablesMoveModal();
			} else {
				this.delete();
			}
		},
		onCloseModal() {
			this.isModalVisible = false;
		}
	}
};
</script>
