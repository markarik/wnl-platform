<template>
	<ul>
		<wnl-manual-plan-node-item
			v-for="(item, index) in nodes"
			:key="item.id"
			:manual-start-dates="manualStartDates"
			:node="item"
			:is-even="index % 2 === 0"
			@change="$emit('change', $event)"
		/>
	</ul>
</template>

<script>
import WnlManualPlanNodeItem from 'js/components/user/plan/ManualPlanNodeItem';

export default {
	components: {
		WnlManualPlanNodeItem,
	},
	props: {
		manualStartDates: {
			type: Array,
			default: () => [],
		},
		nodes: {
			type: Array,
			required: true,
		}
	},
};
</script>
