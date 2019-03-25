<template>
	<div>
		<h4>{{headline}}</h4>

		<template v-if="isEdit">
			<div class="tabs">
				<ul>
					<li :class="{ 'is-active': name === activeTabName }" v-for="(tab, name) in tabs" :key="name">
						<router-link :to="{ hash: `#${name}` }">{{ tab.text }}</router-link>
					</li>
				</ul>
			</div>

			<component :is="activeComponent" :id="id"></component>
		</template>
		<wnl-product-details-editor :id="id" :is-edit="isEdit" v-else/>
	</div>

</template>

<script>

import WnlProductDetailsEditor from './ProductDetailsEditor';
import WnlProductLessonsEditor from './ProductLessonsEditor';
import WnlProductPaymentMethods from './ProductPaymentMethods';

export default {
	components: { WnlProductDetailsEditor },
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
					component: WnlProductDetailsEditor,
					text: 'Szczegóły produktu'
				},
				lessons: {
					component: WnlProductLessonsEditor,
					text: 'Powiązane lekcje'
				},
				payment: {
					component: WnlProductPaymentMethods,
					text: 'Metody płatności'
				},
			}
		};
	},
	computed: {
		isEdit() {
			return this.id !== 'new';
		},
		headline() {
			if (this.isEdit) return `Produkt ${this.id}`;
			return 'Nowy Produkt';
		},
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
