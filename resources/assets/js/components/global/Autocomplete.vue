<template>
	<div class="control">
		<label v-if="label" class="label">{{label}}</label>
		<input
			class="input"
			:value="value"
			:placeholder="placeholder"
			:disable="disabled"
			@input="$emit('input', $event.target.value)"
			@keydown="onKeyDown"
		/>
		<wnl-autocomplete-list
			:items="items"
			@change="$emit('change', $event)"
			@close="$emit('input', '')"
			:isDown="isDown"
			ref="autocomplete"
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
import {KEYS} from 'js/consts/keys';

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
		}
	},
	components: {
		WnlAutocompleteList,
	},
	methods: {
		onKeyDown(evt) {
			if ([KEYS.arrowUp, KEYS.arrowDown, KEYS.esc, KEYS.enter].includes(evt.keyCode)) {
				evt.preventDefault();
				evt.stopPropagation();
			}

			this.$refs.autocomplete.onKeyDown(evt);
		},
	}
};
</script>
