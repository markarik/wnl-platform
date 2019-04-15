<template>
	<div class="nested-set-right">
		<nav class="tabs is-uppercase small">
			<ul>
				<li
					v-for="mode in modes"
					:key="mode.key"
					:class="{'is-active': mode.key === activeMode}"
				>
					<a @click="$emit('setEditorMode', mode.key)">
						<span class="icon is-small"><i :class="['fa', mode.icon]" aria-hidden="true"></i></span>
						<span>{{mode.label}}</span>
					</a>
				</li>
			</ul>
		</nav>

		<slot name="activeView" />
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.nested-set-right
		padding-top: $margin-big
		position: sticky
		top: -30px
</style>

<script>

import { NESTED_SET_EDITOR_MODES } from 'js/consts/nestedSet';

export default {
	props: {
		activeMode: {
			type: String,
			default: NESTED_SET_EDITOR_MODES.ADD,
			validator: (value) => Object.values(NESTED_SET_EDITOR_MODES).includes(value)
		}
	},
	data() {
		return {
			modes: [
				{
					icon: 'fa-plus',
					key: NESTED_SET_EDITOR_MODES.ADD,
					label: 'Dodaj',
				},
				{
					icon: 'fa-pencil',
					key: NESTED_SET_EDITOR_MODES.EDIT,
					label: 'Edytuj',
				},
				// {
				// 	icon: 'fa-compress',
				// 	key: NESTED_SET_EDITOR_MODES.MERGE,
				// 	label: 'Połącz'
				// },
				{
					icon: 'fa-trash',
					key: NESTED_SET_EDITOR_MODES.DELETE,
					label: 'Usuń',
				}
			],
		};
	},
};
</script>
