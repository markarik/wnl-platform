<template>
	<div class="field">
		<ol>
			<draggable
				v-model="inputValue"
				@start="drag=true"
				@end="drag=false"
			>
				<li
					v-for="item in inputValue"
					:key="item"
					class="item"
				>
					<slot :item="formData.included[name][item]" />
				</li>
			</draggable>
		</ol>

		<template v-if="hasErrors">
			<span
				v-for="(error, index) in getErrors"
				:key="index"
				class="help is-danger pre-line"
				v-text="error"
			/>
		</template>
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
	mixins: [formInput],
	props: {
		name: {
			type: String,
		},
	},
	computed: {
		default() {
			return [];
		},
	}
};
</script>
