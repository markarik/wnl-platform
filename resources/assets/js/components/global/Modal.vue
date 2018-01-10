<template>
	<div class="wnl-modal">
		<div class="modal" :class="{ 'is-active': isModalVisible }">
			<div class="modal-background" @click="deactivateModal"></div>
			<div class="modal-content">
				<div class="box">
					<slot></slot>
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
import { mapGetters, mapActions } from 'vuex'

import Avatar from 'js/components/global/Avatar'

export default {
	name: 'Modal',
    computed: {
        ...mapGetters(['isModalVisible']),
    },
	methods: {
		...mapActions(['toggleModal']),
		deactivateModal() {
			this.toggleModal({
				visible: false,
			})
		},
	},
}
</script>
