import { scrollToElement } from 'js/utils/animations'
import { Tween, update } from 'es6-tween';


const highlight = {
	methods: {
		scrollToHighlight() {
			scrollToElement(this.$refs.highlight)
		},
		cleanupRoute() {
			const {[this.highlightableResource]: resourceName, notification, ...query} = this.$route.query
			this.$router.replace({
				...this.$route,
				query
			})
		},
		highlight() {
			const animate = (time) => {
				if (update(time)) requestAnimationFrame(animate)
			}
			const element = {el: this.$refs.highlight, value: 0}
			this.$refs.highlight.style.transition = 'background 5s'

			const tween = new Tween(element)
				.to({value: 1}, 2000)
				.on('update', function() {
					this.object.el.style.background = 'rgba(68, 180, 144, 0.5)';
				})
				.once('complete', () => {
					new Tween(element)
						.to({ value: 0 }, 2000)
						.on('update', function() {
							this.object.el.style.background = '';
						})
						.start()
				})
				.start()

			animate()
		},
		scrollAndHighlight() {
			this.cleanupRoute()
			this.scrollToHighlight()
			this.highlight()
		}
	},
}

export default highlight;
