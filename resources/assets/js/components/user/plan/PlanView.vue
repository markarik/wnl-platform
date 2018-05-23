<template>
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title tabs">
			<ul>
				<li
					v-for="tab in tabs"
					@click="onTabSelect(tab)"
					:class="[tab.isActive ? 'is-active' : '', 'big', 'strong']"
					:key="tab.title"
				>
					<a>{{tab.title}}</a>
				</li>
			</ul>
		</div>
		<component :is="activeView"/>
	</div>
</template>

<style lang="sass" scoped>
	.plan-details
		display: flex
		flex-direction: column
</style>

<script>
	import moment from 'moment';
	import {mapGetters} from 'vuex'
	import {first, last} from 'lodash';
	import LessonsPlanner from './LessonsPlanner';
	import PlannerGuide from './PlannerGuide';

	export default {
		data() {
			return {
				tabs: [
					{
						title: 'Twój Plan Pracy',
						component: LessonsPlanner,
						isActive: true
					},
					{
						title: 'Jak ustawić plan?',
						component: PlannerGuide,
					}
				]
			}
		},
		computed: {
			activeView() {
				return this.tabs.find(tab => tab.isActive).component
			}
		},
		methods: {
			onTabSelect(selectedTab) {
				this.tabs = this.tabs.map(tab => {
					if (selectedTab.title === tab.title) {
						return {
							...tab,
							isActive: true
						}
					}
					return {
						...tab,
						isActive: false
					}
				});
			}
		}
	}
</script>

