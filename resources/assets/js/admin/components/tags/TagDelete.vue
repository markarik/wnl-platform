<template>
	<div>
		<button
			class="button is-danger margin right"
			type="button"
			@click="onClick"
			:disabled="!isDeleteAllowed"
			:title="!isDeleteAllowed && 'Nie można usunąć tego tagu ponieważ należy do taksonomii lub jest kategorią lub taguje lekcję albo stronę'"
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
		id: {
			type: String|Number,
			required: true,
		},
		isDeleteAllowed: {
			type: Boolean,
			default: false,
		},
		taggablesCount: {
			type: Number,
			default: 0,
		}
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
			return window.confirm('Czy na pewno chcesz usunąć tego taga? Ta operacja jest nieodwracalna.');
		},
		async deleteTag() {
			try {
				await axios.delete(getApiUrl(`tags/${this.id}`));
				this.addAutoDismissableAlert({
					text: 'Tag został usunięty',
					type: 'success',
				});

				this.$emit('tagDeleted');
			} catch ({response: {data: {message}}}) {
				this.addAutoDismissableAlert({
					text: message || 'Usuwanie taga nie powiodło się.',
					type: 'error',
				});
			}
		},
		async deleteTagAfterConfirm() {
			if (this.confirmDelete()) {
				this.deleteTag();
			}
		},
		showTaggablesMover() {
			this.isTaggablesMoverVisible = true;
		},
		async onClick() {
			if (this.taggablesCount > 0) {
				this.showTaggablesMover();
			} else {
				if (this.confirmDelete()) {
					this.deleteTag();
				}
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
