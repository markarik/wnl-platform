<template>
	<div>
		<button
			class="button is-danger margin right"
			type="button"
			@click="onClick"
			:disabled="!isDeleteAllowed"
		>Usuń</button>
		<wnl-modal
			:isModalVisible="isTaggablesMoverVisible"
			@closeModal="onCloseModal"
			v-if="isTaggablesMoverVisible"
		>
			<p class="field">
				Dla tego taga istnieje {{taggablesCount}} powiązań.
				Chcesz przypisać powiązania do innego taga, czy je usunąć?
			</p>
			<wnl-taggables-mover
				:beforeSubmit="confirmDelete"
				:sourceTagId="id"
				@taggablesMoved="onTaggablesMoved"
			>Przenieś powiązania i usuń taga</wnl-taggables-mover>
			<div class="field">
				<button
					class="button is-danger"
					@click="deleteTagAfterConfirm"
				>Usuń powiązania i taga</button>
			</div>
		</wnl-modal>
	</div>
</template>

<script>
import axios from 'axios';
import {mapActions} from 'vuex';
import {getApiUrl} from 'js/utils/env';
import Modal from 'js/components/global/Modal';
import WnlTaggablesMover from 'js/admin/components/tags/TaggablesMover';

export default {
	name: 'TagDelete',
	components: {
		'wnl-modal': Modal,
		'wnl-taggables-mover': WnlTaggablesMover,
	},
	props: {
		id: {},
		isDeleteAllowed: Boolean,
		taggablesCount: Number
	},
	data: () => {
		return {
			isTaggablesMoverVisible: false,
		};
	},
	computed: {},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		confirmDelete() {
			return new Promise(function (resolve, reject) {
				let confirmed = window.confirm('Czy na pewno chcesz usunąć tego taga? Ta operacja jest nieodwracalna.');

				return confirmed ? resolve() : reject();
			});
		},
		deleteTag() {
			return axios.delete(getApiUrl(`tags/${this.id}`))
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
		async deleteTagAfterConfirm() {
			try {
				await this.confirmDelete();
			} catch (e) {}

			this.deleteTag();
		},
		showTaggablesMover() {
			this.isTaggablesMoverVisible = true;
		},
		async onClick() {
			if (this.taggablesCount > 0) {
				this.showTaggablesMover();
			} else {
				try {
					await this.confirmDelete();
				} catch (e) {
					return;
				}

				this.deleteTag();
			}
		},
		onCloseModal() {
			this.isTaggablesMoverVisible = false;
		},
		onTaggablesMoved() {
			this.deleteTag();
		}
	}
};
</script>
