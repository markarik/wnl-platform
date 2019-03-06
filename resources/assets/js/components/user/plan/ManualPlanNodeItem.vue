<template>
	<li :class="{
	'is-group': isGroup,
	'is-lesson': !isGroup,
	'is-even': isEven,
	}">
		<template v-if="isGroup">
			<span class="item-toggle" @click="isOpen = !isOpen">
				<span class="icon is-small">
					<i class="toggle fa fa-angle-down"
						 :class="{'fa-rotate-180': isOpen}"></i>
				</span>
				<span>{{node.model.name}}</span>
				<span class="subitems-count">
					({{childrenNodes.length}})
				</span>
			</span>

			<wnl-manual-plan-nodes-list
				v-if="isOpen"
				:nodes="childrenNodes"
				@change="$emit('change', $event)"
			/>
		</template>

		<template v-else>
			<span class="lesson-name label"
						:class="{'is-grayed-out': !node.model.isAccessible}"
			>{{node.model.name}}</span>
			<div class="lesson-left-side">
				<div class="not-accesible" v-if="!node.model.isAccessible">
					{{ $t('lessonsAvailability.lessonNotAvilable') }}
				</div>
				<div class="datepicker" v-else>
					<wnl-datepicker
						:class="{'hasColorBackground': isEven}"
						:value="startDate"
						:subitem-id="node.model.id"
						:config="datepickerConfig"
						@onChange="(payload) => $emit('change', {newStartDate: payload, lesson: node.model})"
					/>
				</div>
			</div>
		</template>
	</li>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.item-toggle
		color: $color-sky-blue
		cursor: pointer
		text-align: center
		text-transform: uppercase
		margin-bottom: $margin-small
		width: 100%
		.icon
			color: $color-darkest-gray
		.subitems-count
			color: $color-background-gray
			font-size: $font-size-minus-2

	.is-group
		margin-bottom: $margin-base

	.is-lesson
		display: flex
		flex-direction: row-reverse
		justify-content: space-between
		padding-bottom: $margin-small
		padding-top: $margin-small
		min-height: 35px
		&.is-even
			background-color: $color-background-lightest-gray
		.lesson-name
			align-self: flex-end
			color: $color-darkest-gray
			width: 65%
			&.is-grayed-out
				color: $color-gray
		.lesson-left-side
			align-items: center
			display: flex
			margin-right: $margin-small
			.not-accesible
				color: $color-gray
				font-size: $font-size-plus-1
				text-align: center
				cursor: not-allowed
				min-width: 260px

</style>


<script>
import {mapGetters} from 'vuex';
import {getModelByResource} from 'js/utils/config';
import {resources} from 'js/utils/constants';
import WnlDatepicker from 'js/components/global/Datepicker';

export default {
	data() {
		return {
			isOpen: false,
			datepickerConfig: {
				altInput: true,
				disableMobile: true,
			},
		};
	},
	props: {
		node: {
			type: Object,
			required: true,
		},
		isEven: {
			type: Boolean,
			default: true,
		}
	},
	components: {
		WnlDatepicker,
	},
	computed: {
		...mapGetters('course', [
			'getChildrenNodes'
		]),
		childrenNodes() {
			return this.getChildrenNodes(this.node.id);
		},
		isGroup() {
			return getModelByResource(resources.groups) === this.node.structurable_type;
		},
		startDate() {
			return this.node.model.startDate ? new Date (this.node.model.startDate*1000) : new Date();
		},
	},
	methods: {

	},
	beforeCreate: function () {
		// https://vuejs.org/v2/guide/components-edge-cases.html#Circular-References-Between-Components
		this.$options.components.WnlManualPlanNodesList = require('./ManualPlanNodesList.vue').default;
	}
};
</script>
