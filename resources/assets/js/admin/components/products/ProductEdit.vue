<template>
	<div>
		<h4>Produkt #{{ id }}</h4>

		<div class="tabs">
			<ul>
				<li :class="{ 'is-active': name === activeTabName }" v-for="(tab, name) in tabs" :key="name">
					<router-link :to="{ hash: `#${name}` }">{{ tab.text }}</router-link>
				</li>
			</ul>
		</div>

		<component :is="activeComponent" :id="id"></component>
	</div>

</template>

<script>

import ProductDetailsEditor from './ProductDetailsEditor';
import ProductLessonsEditor from './ProductLessonsEditor';

export default {
	props: {
		id: {
			required: true,
			type: [Number, String],
		}
	},
	data() {
		return {
			tabs: {
				details: {
					component: ProductDetailsEditor,
					text: 'Szczegóły produktu'
				},
				lessons: {
					component: ProductLessonsEditor,
					text: 'Powiązane lekcje'
				},
			}
		};
	},
	computed: {
		activeComponent() {
			return this.tabs[this.activeTabName].component;
		},
		activeTabName() {
			const hash = this.$route.hash.replace('#', '');
			const tabNames = Object.keys(this.tabs);
			return tabNames.includes(hash) ? hash : tabNames[0];
		}
	}
};
</script>
