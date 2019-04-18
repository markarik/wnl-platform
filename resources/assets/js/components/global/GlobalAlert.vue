<template>
	<div>
		<div class="notification" :class="typeToClass(type)">
			<slot />
			<button
				v-if="dismissable"
				class="delete"
				@click="$emit('onDismiss', {id: id})"
			/>
		</div>
	</div>
</template>
<script>
import { ALERT_TYPES } from 'js/consts/alert';

export default {
	props: {
		type: {
			type: String
		},
		dismissable: {
			type: Boolean,
			default: true
		},
		id: {
			type: String,
			default: ''
		}
	},
	methods: {
		typeToClass(type) {
			switch(type) {
			case ALERT_TYPES.ERROR:
				return 'is-danger';
			case ALERT_TYPES.SUCCESS:
				return 'is-success';
			case ALERT_TYPES.INFO:
				return 'is-info';
			case ALERT_TYPES.WARNING:
				return 'is-warning';
			default:
				return 'is-primary';
			}
		},
	}
};
</script>
