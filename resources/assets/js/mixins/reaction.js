import { mapActions } from 'vuex';

export const reaction = {
	props: ['module', 'reactableResource', 'reactableId', 'updateLocally', 'state', 'reactionsDisabled'],
	data() {
		return {
			isLoading: false,
			isReady: false,
			wasJustClicked: false,
		};
	},
	computed: {
		hasReacted() {
			return this.state.hasReacted;
		},
		count() {
			return this.state.count;
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		setReaction(payload) {
			return this.$store.dispatch(`${this.module}/setReaction`, payload);
		},
		getReaction(resource, id, reactionName) {
			return this.$store.getters[`${this.module}/getReaction`](resource, id, reactionName);
		},
		async toggleReaction() {
			if (this.isLoading || this.reactionsDisabled) {
				return false;
			}
			this.wasJustClicked = true;
			this.isLoading = true;

			try {
				await this.setReaction({
					reactableResource: this.reactableResource,
					reactableId: this.reactableId,
					reaction: this.name,
					hasReacted: this.hasReacted,
					count: this.count,
					vuexState: this.getReaction(this.reactableResource, this.reactableId, this.name)
				});
			} catch (error) {
				$wnl.logger.error(error);

				if (error.response.status === 404) {
					this.addAutoDismissableAlert({
						type: 'warning',
						text: this.$t('ui.error.notFound'),
					});
				} else {
					this.addAlert({
						type: 'error',
						text: 'Niestety, nie udało nam się dokonać zapisu. :( Problem jest nam znany i cały czas nad nim pracujemy. Tymczasowo, żeby problem ustąpił, możesz odświeżyć stronę. :)',
					});
				}
			}

			this.isLoading = false;
			this.wasJustClicked = false;
		},
	},
	mounted() {
		this.isReady = true;
	}
};
