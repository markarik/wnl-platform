<template>
	<div
		v-if="term.id"
		class="center"
	>
		<p class="margin bottom">
			Czy na pewno chcesz usunąć pojęcie <em><strong>{{term.tag.name}}</strong></em> wraz z potomkami?
		</p>
		<button
			class="button is-danger"
			:disabled="isSaving"
			@click="onDelete"
		>Usuń pojęcie</button>
	</div>
	<div v-else class="notification is-info">
		<span class="icon">
			<i class="fa fa-info-circle"></i>
		</span>
		Najpierw wybierz pojęcia
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	.center
		text-align: center
</style>

<script>
import {mapActions, mapGetters, mapState} from 'vuex';

export default {
	computed: {
		...mapGetters('taxonomyTerms', ['termById']),
		...mapState('taxonomyTerms', ['isSaving', 'selectedTerms']),
		term() {
			if (this.selectedTerms.length === 0) {
				return {};
			}

			// TODO figure out multiple terms selected
			return this.termById(this.selectedTerms[0]);
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('taxonomyTerms', {
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
