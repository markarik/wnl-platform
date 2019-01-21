import Vue from 'vue';
import {scrollToElement} from 'js/utils/animations';

export default {
	methods: {
		async scrollToTaxonomyTerm(term) {
			await Vue.nextTick();

			const termElement = document.getElementById(`term-${term.id}`);

			// nothing to scroll to
			if (!termElement) return;

			// wait for collapse animation to finish
			setTimeout(
				() => scrollToElement(
					termElement,
					150,
					500,
					document.querySelector('.admin-right')
				), 300);
		}
	},
};
