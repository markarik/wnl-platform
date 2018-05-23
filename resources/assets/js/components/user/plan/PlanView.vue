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

<script>
	import LessonsPlanner from './LessonsPlanner';
	import PlannerGuarantee from './PlanGuarantee.vue';
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
						title: 'Jak zmienić plan?',
						component: PlannerGuide,
					},
					{
						title: 'Gwarancja Satysfakcji',
						component: PlannerGuarantee,
					},
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
