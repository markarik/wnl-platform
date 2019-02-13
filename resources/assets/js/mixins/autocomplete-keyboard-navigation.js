import {KEYS} from 'js/consts/keys';

export default {
	data() {
		return {
			activeIndex: -1,
		};
	},
	computed: {
		hasItems() {
			return this.items && this.items.length;
		},
	},
	methods: {
		onKeyDown(evt) {
			switch (evt.keyCode) {
			case KEYS.arrowUp:
				this.onArrowUp(evt);
				break;
			case KEYS.arrowDown:
				this.onArrowDown(evt);
				break;
			case KEYS.enter:
				this.onEnter(evt);
				break;
			case KEYS.esc:
				this.onEsc(evt);
				break;
			}
		},
		onArrowUp(evt) {
			if (!this.hasItems) return;

			this.activeIndex--;
			if (this.activeIndex < 0) {

				this.activeIndex = this.items.length - 1;
			}

			//Something would steal the focus back to the Quill input when if we'd do it synchronously
			this.$nextTick(() => {
				this.$el.focus();
			});

			this.killEvent(evt);
		},
		onArrowDown(evt) {
			if (!this.hasItems) return;

			this.activeIndex++;
			if (this.activeIndex >= this.items.length) {
				this.activeIndex = 0;
			}

			//Something would steal the focus back to the Quill input when if we'd do it synchronously
			this.$nextTick(() => {
				this.$el.focus();
			});

			this.killEvent(evt);
		},

		onEnter(evt) {
			const activeIndex = this.activeIndex;

			if (activeIndex < 0) return;
			this.$emit('change', this.items[activeIndex]);

			this.killEvent(evt);
			return false;
		},

		onEsc(evt) {
			this.$emit('input', '');

			this.killEvent(evt);
			return false;
		},

		killEvent(evt) {
			evt.preventDefault();
			evt.stopPropagation();
		}
	},
	watch: {
		items() {
			this.activeIndex = -1;
		}
	}
};
