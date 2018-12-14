<template>
	<div class="field">
		<ol>
			<draggable v-model="inputValue" @start="drag=true" @end="drag=false">
				<li
						class="item"
						v-for="item in inputValue"
				>
					<slot v-bind:item="formData.included[name][item]" />
				</li>
			</draggable>
		</ol>

		<span class="help is-danger"
			v-if="hasErrors"
			v-for="(error, index) in getErrors"
			v-text="error"
			:key="index"></span>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.item
		border-top: $border-light-gray
		cursor: move
		padding: $margin-base 0
		margin-left: $margin-base
</style>

<script>
import draggable from 'vuedraggable';

import { formInput } from 'js/mixins/form-input';

export default {
	name: 'Sortable',
	components: {
		draggable,
	},
	props: {
		name: {
			type: String,
		},
	},
	mixins: [formInput],
	computed: {
		default() {
			return [];
		},
	}
};
</script>
