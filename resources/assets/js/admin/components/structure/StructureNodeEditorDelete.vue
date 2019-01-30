<template>
	<div
		v-if="currentNode"
		class="has-text-centered"
	>
		<p class="margin bottom">
			Czy na pewno chcesz usunąć <em><strong>{{currentNode.structurable.name}}</strong></em> wraz z potomkami?
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
		...mapGetters('courseStructure', ['nodeById', 'currentNode']),
		...mapState('courseStructure', ['isSaving']),
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('courseStructure', {
			'deleteNode': 'delete',
			'selectNodes': 'select'
		}),
		async onDelete() {
			try {
				await this.deleteNode(this.currentNode);
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
