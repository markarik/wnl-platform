<template>
	<div
		v-if="node"
		class="has-text-centered"
	>
		<p class="margin bottom">
			Czy na pewno chcesz usunąć <em><strong>{{node.structurable.name}}</strong></em> wraz z potomkami?
		</p>
		<button
			class="button is-danger"
			:disabled="isSaving"
			@click="onDelete"
		>
			<span class="icon is-small"><i class="fa fa-trash" aria-hidden="true"></i></span>
			<span>Usuń</span>
		</button>
	</div>
	<div v-else class="notification is-info">
		<span class="icon">
			<i class="fa fa-info-circle"></i>
		</span>
		Najpierw wybierz gałąź struktury
	</div>
</template>

<script>
import {mapActions, mapGetters, mapState} from 'vuex';

export default {
	computed: {
		...mapGetters('courseStructure', ['nodeById']),
		...mapState('courseStructure', ['isSaving', 'selectedNodes']),
		node() {
			if (this.selectedNodes.length === 0) {
				return null;
			}

			return this.nodeById(this.selectedNodes[0]);
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('courseStructure', {
			'deleteNode': 'delete',
			'selectNodes': 'select'
		}),
		async onDelete() {
			try {
				await this.deleteNode(this.node);
				this.selectNodes([]);

				this.addAutoDismissableAlert({
					text: 'Usunięto pomyślnie!',
					type: 'success'
				});
			} catch (error) {
				$wnl.logger.capture(error);

				this.addAutoDismissableAlert({
					text: 'Ups, coś poszło nie tak, spróbuj ponownie.',
					type: 'error',
				});
			}
		},
	},
};
</script>
