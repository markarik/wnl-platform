<template>
	<div class="control">
		<label v-if="label" class="label">{{label}}</label>
		<input
			ref="input"
			class="input"
			:value="value"
			:placeholder="placeholder"
			:disable="disabled"
			@input="$emit('input', $event.target.value)"
			@keydown="onKeyDown"
			@blur="$emit('blur', $event)"
		/>
		<wnl-autocomplete-list
			:items="items"
			:active-index="activeIndex"
			:is-down="isDown"
			@change="$emit('change', $event)"
		>
			<template slot-scope="slotProps">
				<slot :item="slotProps.item"></slot>
			</template>
			<template slot="footer">
				<slot name="footer"></slot>
			</template>
		</wnl-autocomplete-list>
	</div>
</template>

<script>
import WnlAutocompleteList from 'js/components/global/AutocompleteList';
import WnlAutocompleteKeyboardNavigation from 'js/mixins/autocomplete-keyboard-navigation';

export default {
	props: {
		value: {
			type: String,
			default: '',
		},
		items: {
			type: Array,
			default: () => [],
		},
		isDown: {
			type: Boolean,
			default: true,
		},
		disabled: {
			type: Boolean,
			default: false,
		},
		placeholder: {
			type: String,
			default: 'Zacznij pisać aby zobaczyć podpowiedzi',
		},
		label: {
			type: String,
			default: '',
		},
		isFocused: {
			type: Boolean,
			default: false,
		},
	},
	components: {
		WnlAutocompleteList,
	},
	mixins: [WnlAutocompleteKeyboardNavigation],
	watch: {
		async isFocused(isFocused) {
			if (isFocused) {
				this.$refs.input.focus();
			}
		},
	},
};
</script>
