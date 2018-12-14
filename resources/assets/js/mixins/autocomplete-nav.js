export default {
	computed: {
		hasItems() {
			return this.items && this.items.length;
		},
		activeIndex() {
			return this.items.findIndex((item) => item.active);
		}
	},
	methods: {
		onKeyDown(evt) {
			switch (evt.keyCode) {
			case 38:
				evt.stopPropagation();
				this.onArrowUp(evt);
				break;
			case 40:
				evt.stopPropagation();
				this.onArrowDown(evt);
				break;
			case 13:
				this.onEnter(evt);
				break;
			case 27:
				this.$emit('close');
				break;
			}
		},
		onArrowUp() {
			if (!this.hasItems) return;

			const activeIndex = this.activeIndex;
			if (activeIndex <= 0) {
				this.$set(this.items[this.items.length - 1], 'active', true);
			} else {
				this.$set(this.items[activeIndex - 1], 'active', true);
			}

			if (activeIndex >= 0) this.$set(this.items[activeIndex], 'active', false);

			//Something would steal the focus back to the Quill input when if we'd do it synchronously
			this.$nextTick(() => {
				this.$el.focus();
			});
		},
		onArrowDown() {
			if (!this.hasItems) return;

			const activeIndex = this.activeIndex;

			if (activeIndex < 0 || activeIndex === this.items.length - 1) {
				this.$set(this.items[0], 'active', true);
			} else {
				this.$set(this.items[activeIndex + 1], 'active', true);
			}

			if (activeIndex > -1) this.$set(this.items[activeIndex], 'active', false);

			//Something would steal the focus back to the Quill input when if we'd do it synchronously
			this.$nextTick(() => {
				this.$el.focus();
			});
		},

		onEnter(evt) {
			this.$emit('close');
			const activeIndex = this.activeIndex;

			if (activeIndex < 0) return;

			this.$set(this.items[activeIndex], 'active', false);
			this.onItemChosen(this.items[activeIndex], activeIndex);

			evt.preventDefault();
			evt.stopPropagation();
			return false;
		},
	}
};
