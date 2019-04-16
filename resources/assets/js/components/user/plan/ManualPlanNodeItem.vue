<template>
	<li
		v-if="!isGroup || childrenNodes.length > 0"
		:class="{
			'is-group': isGroup,
			'is-lesson': !isGroup,
			'is-even': isEven,
		}"
	>
		<template v-if="isGroup">
			<span class="item-toggle" @click="isOpen = !isOpen">
				<span class="icon is-small">
					<i class="toggle fa fa-angle-down" :class="{'fa-rotate-180': isOpen}" />
				</span>
				<span>{{node.model.name}}</span>
				<span class="subitems-count">
					({{childrenNodes.length}})
				</span>
			</span>

			<wnl-manual-plan-nodes-list
				v-if="isOpen"
				class="nodes-list"
				:manual-start-dates="manualStartDates"
				:nodes="childrenNodes"
				@change="$emit('change', $event)"
			/>
		</template>

		<template v-else>
			<div class="lesson-left-side">
				<div v-if="!node.model.isAccessible" class="not-accesible">
					{{$t('lessonsAvailability.lessonNotAvailable')}}
				</div>
				<div v-else :class="{'is-default-start-date': isDefaultStartDate}">
					<wnl-datepicker
						:class="{'hasColorBackground': isEven}"
						:value="startDate"
						:config="datepickerConfig"
						@onChange="(payload) => $emit('change', {newStartDate: payload, lesson: node.model})"
					/>
					<div v-if="isDefaultStartDate" class="lesson-default-start-date">(domyślna)</div>
				</div>
			</div>
			<span
				class="lesson-name label"
				:class="{'is-grayed-out': !node.model.isAccessible}"
			>{{node.model.name}}</span>
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
		align-items: center;
		display: flex
		justify-content: space-between
		min-height: 35px
		padding-bottom: $margin-small
		padding-top: $margin-small

		&.is-even
			background-color: $color-background-lighter-gray

	.lesson-name
		color: $color-darkest-gray
		width: 65%

		&.is-grayed-out
			color: $color-gray

	.lesson-left-side
		text-align: center
		display: flex
		flex-direction: column
		margin-right: $margin-small
		min-width: 220px

	.not-accesible
		color: $color-gray
		cursor: not-allowed
		font-size: $font-size-plus-1
		margin: $margin-medium 0
		text-align: center

	.lesson-default-start-date
		color: $color-gray
		font-size: $font-size-minus-2
		margin-top: -5px

	/deep/ .is-default-start-date .datepicker
		font-size: $font-size-minus-1

	.nodes-list
		margin-left: $margin-big
		margin-bottom: $margin-base
</style>


<script>
import { mapGetters } from 'vuex';
import { getModelByResource } from 'js/utils/config';
import { resources } from 'js/utils/constants';
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
		manualStartDates: {
			type: Array,
			default: () => [],
		},
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
			return this.getChildrenNodes(this.node.id).filter(
				node => node.structurable_type === getModelByResource(resources.groups) || node.model.is_required
			);
		},
		isDefaultStartDate() {
			return this.node.model.isDefaultStartDate &&
				!this.manualStartDates.some((lessonDates) => lessonDates.lessonId === this.node.structurable_id);
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
