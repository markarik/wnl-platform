<template>
	<div>
		<button
			class="button is-danger margin right"
			type="button"
			@click="onClick"
			:disabled="!isDeleteAllowed"
		>Usuń</button>
		<wnl-modal :isModalVisible="isModalVisible" @closeModal="onCloseModal" v-if="isModalVisible">
			<p>
				Dla tego taga istnieje {{taggablesCount}} powiązań.
				Chcesz usunąć wszystkie powiązania, czy przypisać je do innego taga?
			</p>
			<button
				@click="deleteTag"
			>Usuń taga i wszystkie powiązania</button>

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
		deleteTag() {
			if (!confirm('Czy na pewno chcesz usunąć tego taga? Ta operacja jest nieodwracalna.')) {
				return;
			}

			axios.delete(getApiUrl(`tags/${this.id}`))
				.then(() => {
					this.addAutoDismissableAlert({
						text: 'Tag został usunięty',
						type: 'success',
					});

					this.$emit('tagDeleted');
				})
				.catch(({response: {data: {message = 'Usuwanie taga nie powiodło się.'}}}) => {
					this.addAutoDismissableAlert({
						text: message,
						type: 'error',
					});
				});
		},
		showTaggablesMoveModal() {
			this.isModalVisible = true;
		},
		onClick() {
			if (this.taggablesCount > 0) {
				this.showTaggablesMoveModal();
			} else {
				this.deleteTag();
			}
		},
		onCloseModal() {
			this.isModalVisible = false;
		}
	}
};
</script>
