<template>
	<div class="wnl-course-navigation" :class="{ mobile: isMobileNavigation }">
		<div class="items">
			<wnl-group-item v-for="item in rootItems"
				:item="item"
				:key="item.id"
			/>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-course-navigation
		&.mobile
			height: auto
			width: 100%
</style>

<script>
import { mapGetters, mapState } from 'vuex';
import WnlGroupItem from 'js/components/course/navigation/GroupItem';

export default {
	components: {
		WnlGroupItem
	},
	computed: {
		...mapGetters(['isMobileNavigation']),
		...mapState('course', ['structure']),
		rootItems() {
			return this.structure.filter(item => !item.parent_id);
		}
	},
};
</script>
