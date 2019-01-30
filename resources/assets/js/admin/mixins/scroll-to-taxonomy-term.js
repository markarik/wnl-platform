import Vue from 'vue';
import {scrollToElement} from 'js/utils/animations';

export default {
	methods: {
		async scrollToTaxonomyTerm(term) {
			await Vue.nextTick();
			// wait for collapse animation to finish
			setTimeout(
				() => scrollToElement(
					document.getElementById(`term-${term.id}`),
					150,
					500,
					document.querySelector('.admin-right')
				), 300);
		}
	},
};
