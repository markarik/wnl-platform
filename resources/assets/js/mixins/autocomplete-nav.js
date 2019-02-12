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
				evt.preventDefault();
				evt.stopPropagation();
				this.onArrowUp(evt);
				break;
			case KEYS.arrowDown:
				evt.preventDefault();
				evt.stopPropagation();
				this.onArrowDown(evt);
				break;
			case KEYS.enter:
				this.onEnter(evt);
				break;
			case KEYS.esc:
				this.onEsc(evt);
				// TODO handle close
				this.$emit('close');
				break;
			}
		},
		onArrowUp() {
			if (!this.hasItems) return;

			this.activeIndex--;
			if (this.activeIndex < 0) {

				this.activeIndex = this.items.length - 1;
			}

			//Something would steal the focus back to the Quill input when if we'd do it synchronously
			this.$nextTick(() => {
				this.$el.focus();
			});
		},
		onArrowDown() {
			if (!this.hasItems) return;

			this.activeIndex++;
			if (this.activeIndex >= this.items.length) {
				this.activeIndex = 0;
			}

			//Something would steal the focus back to the Quill input when if we'd do it synchronously
			this.$nextTick(() => {
				this.$el.focus();
			});
		},

		onEnter(evt) {
			this.$emit('close');
			const activeIndex = this.activeIndex;

			if (activeIndex < 0) return;

			this.$emit('change', this.items[activeIndex]);
			// TODO select in Quill

			evt.preventDefault();
			evt.stopPropagation();
			return false;
		},

		onEsc(evt) {
			this.$emit('close');
			this.$emit('input', '')

			evt.preventDefault();
			evt.stopPropagation();
			return false;
		},
	},
	watch: {
		items() {
			this.activeIndex = -1;
		}
	}
};
