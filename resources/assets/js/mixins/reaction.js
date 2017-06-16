import { mapGetters, mapActions } from 'vuex'

export const reaction = {
	props: ['module', 'reactableResource', 'reactableId'],
	data() {
		return {
			isLoading: false
		}
	},
	computed: {
		reaction() {
			return this.$store.getters[`${this.module}/getReaction`](this.reactableResource, this.reactableId, this.name)
		},
		count() {
			return this.reaction.count
		},
	},
	methods: {
		setReaction(payload) {
			return this.$store.dispatch(`${this.module}/setReaction`, payload)
		},
		toggleReaction() {
			if (this.isLoading) {
				return false
			}
			this.isLoading = true
			this.setReaction({
				reactableResource: this.reactableResource,
				reactableId: this.reactableId,
				reaction: this.name,
				hasReacted: this.reaction.hasReacted,
			}).then((response) => {
				this.isLoading = false
			})
			.catch((error) => {
				$wnl.logger.error(error)
				this.isLoading = false
			})
		},
	},
}
