import { mapGetters, mapActions } from 'vuex'
import { SET_REACTION } from 'js/store/mutations-types'

export const reaction = {
	props: ['module', 'reactableResource', 'reactableId', 'updateLocally', 'state'],
	data() {
		return {
			isLoading: false,
			isReady: false,
			wasJustClicked: false,
		}
	},
	computed: {
		hasReacted() {
			return this.state.hasReacted
		},
		count() {
			return this.state.count || 0
		}
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
				this.isLoading = false
				this.wasJustClicked = false

				this.mutation(SET_REACTION, {
					count: this.hasReacted ? this.count-- : this.count++,
					hasReacted: !this.hasReacted,
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
		this.isReady = true
	}
}
