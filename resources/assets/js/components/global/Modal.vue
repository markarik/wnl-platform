<template>
	<div class="wnl-modal">
		<div class="modal">
			<div class="modal-background" @click="deactivateModal"></div>
			<div class="modal-content">
				<div class="box">
					<component :is="props.component" v-bind="props.props"/>
				</div>
			</div>
			<button class="modal-close is-large" aria-label="close" @click="deactivateModal"></button>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-modal
		line-height: $line-height-base
		font-size: $font-size-base

	.modal
		z-index: $z-index-alerts

</style>

<script>
import Avatar from 'js/components/global/Avatar'

export default {
	name: 'UserProfileModal',
	props: ['author', 'userId'],
	data() {
		return {
			showModal: false,
		}
	},
	methods: {
		deactivateModal() {
			this.showModal = false
		},
	},
}
</script>
