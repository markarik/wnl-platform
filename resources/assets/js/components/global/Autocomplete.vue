<template>
	<div class="control">
		<label v-if="label" class="label">{{label}}</label>
		<input class="input" :value="value" @input="$emit('input', $event.target.value)" :placeholder="placeholder" />
		<wnl-autocomplete-list
			:items="items"
			@change="$emit('change', $event)"
			:isDown="isDown"
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
	}
};
</script>
