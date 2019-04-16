import { mapActions } from 'vuex';

export var breadcrumb = {
	methods: {
		...mapActions(['addBreadcrumb', 'removeBreadcrumb'])
	},
	mounted() {
		if (typeof this.breadcrumb === 'object') {
			this.addBreadcrumb(this.breadcrumb);
		}
	},
	beforeDestroy() {
		if (typeof this.breadcrumb === 'object') {
			this.removeBreadcrumb(this.breadcrumb.text);
		}
	}
};
