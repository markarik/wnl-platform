<template>
	<div class="wnl-modal">
		<div class="modal" :class="{ 'is-active': isModalVisible }">
			<div class="modal-background" @click="$emit('closeModal')"></div>
			<div class="modal-content">
				<div class="box">
					<slot></slot>
				</div>
			</div>
			<button class="modal-close is-large" aria-label="close" @click="$emit('closeModal')"></button>
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
import { mapMutations } from 'vuex'
import {UI_TOGGLE_MODAL as modalVisible} from 'js/store/mutations-types'

export default {
	name: 'Modal',
	props: {
		isModalVisible: {
			type: Boolean,
			default: false
		}
	},
	methods: {
		...mapMutations({modalVisible})
	},
	mounted() {
		if (this.isModalVisible) return this.modalVisible(true)
	},
	beforeDestroy() {
		return this.modalVisible(false)
	},
	watch: {
		isModalVisible() {
			if (this.isModalVisible) return this.modalVisible(true)
			return this.modalVisible(false)
		}
	}
}
</script>
