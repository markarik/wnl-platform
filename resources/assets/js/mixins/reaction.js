import { mapGetters, mapActions } from 'vuex'
import { SET_REACTION } from 'js/store/mutations-types'

export const reaction = {
	props: ['module', 'reactableResource', 'reactableId', 'updateLocally'],
	data() {
		return {
			isLoading: false,
			isReady: false,
			count: 0,
			hasReacted: false,
			wasJustClicked: false,
		}
	},
	computed: {
		reaction() {
			return this.$store.getters[`${this.module}/getReaction`](this.reactableResource, this.reactableId, this.name)
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
			this.wasJustClicked = true
			this.isLoading = true
			this.setReaction({
				reactableResource: this.reactableResource,
				reactableId: this.reactableId,
				reaction: this.name,
				hasReacted: this.hasReacted,
			}).then((response) => {
				this.hasReacted ? this.count-- : this.count++
				this.hasReacted = !this.hasReacted
				this.isLoading = false
				this.wasJustClicked = false

				this.mutation(SET_REACTION, {
					count: this.count,
					hasReacted: this.hasReacted,
					reactableResource: this.reactableResource,
					reactableId: this.reactableId,
					reaction: this.name,
				})
			})
			.catch((error) => {
				$wnl.logger.error(error)
				this.isLoading = false
				this.wasJustClicked = false
			})
		},
		mutation(name, payload) {
			this.$store.commit(`${this.module}/${name}`, payload)
		}
	},
	mounted() {
		this.count = this.reaction.count
		this.hasReacted = this.reaction.hasReacted
		this.isReady = true
	}
}
