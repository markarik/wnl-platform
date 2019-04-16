import { scrollToElement, scrollToY } from 'js/utils/animations';
import { Tween, update } from 'es6-tween';


const highlight = {
	methods: {
		scrollToHighlight(scrollable = false) {
			scrollToElement(this.$refs.highlight, 150, 500, scrollable);
		},
		cleanupRoute(paramsToUnset = this.highlightableResources) {
			const { notification, ...queryParams } = this.$route.query;
			let query = {};

			Object.keys(this.$route.query).forEach((param) => {
				if (!paramsToUnset.includes(param)) query = { ...query, [param]: queryParams[param] };
			});

			this.$router.replace({
				...this.$route,
				query
			});
		},
		highlight() {
			const animate = (time) => {
				if (update(time)) requestAnimationFrame(animate);
			};
			const element = { el: this.$refs.highlight, value: 0 };
			this.$refs.highlight.style.transition = 'background 5s';

			new Tween(element)
				.to({ value: 1 }, 2000)
				.on('update', function() {
					this.object.el.style.background = 'rgba(15, 150, 152, 0.2)';
				})
				.once('complete', () => {
					new Tween(element)
						.to({ value: 0 }, 2000)
						.on('update', function() {
							this.object.el.style.background = '';
						})
						.start();
				})
				.start();

			animate();
		},
		scrollAndHighlight(paramsToUnset, scrollable = false) {
			this.cleanupRoute(paramsToUnset);
			this.scrollToHighlight(scrollable);
			this.highlight();
		},
		scrollToPositionAndHighlight(paramsToUnset, scrollY = 0, scrollable = false) {
			this.cleanupRoute(paramsToUnset);
			scrollToY(scrollY, 500, scrollable);
			this.highlight();
		}
	},
};

export default highlight;
