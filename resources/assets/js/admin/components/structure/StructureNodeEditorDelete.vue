<template>
	<div
		v-if="term"
		class="has-text-centered"
	>
		<p class="margin bottom">
			Czy na pewno chcesz usunąć pojęcie <em><strong>{{term.structurable.name}}</strong></em> wraz z potomkami?
		</p>
		<button
			class="button is-danger"
			:disabled="isSaving"
			@click="onDelete"
		>
			<span class="icon is-small"><i class="fa fa-trash" aria-hidden="true"></i></span>
			<span>Usuń pojęcie</span>
		</button>
	</div>
	<div v-else class="notification is-info">
		<span class="icon">
			<i class="fa fa-info-circle"></i>
		</span>
		Najpierw wybierz pojęcie
	</div>
</template>

<script>
import {mapActions, mapGetters, mapState} from 'vuex';

export default {
	computed: {
		...mapGetters('courseStructure', ['termById']),
		...mapState('courseStructure', ['isSaving', 'selectedTerms']),
		term() {
			if (this.selectedTerms.length === 0) {
				return null;
			}

			// TODO figure out multiple terms selected
			return this.termById(this.selectedTerms[0]);
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('courseStructure', {
			'deleteTerm': 'delete',
			'selectTerms': 'select'
		}),
		async onDelete() {
			try {
				await this.deleteTerm(this.term);
				this.selectTerms([]);

				this.addAutoDismissableAlert({
					text: 'Usunięto pojęcie!',
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
