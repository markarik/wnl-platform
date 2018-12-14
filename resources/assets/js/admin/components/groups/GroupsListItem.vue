<template>
	<li class="margin bottom">
		<router-link :to="to">{{id}}. {{name}}</router-link>
	</li>
</template>

<script>
	export default {
		name: 'GroupsListItem',
		props: ['id', 'name'],
		computed: {
			to() {
				return {
					name: 'group-edit',
					params: {
						id: this.id,
					},
				}
			},
		},
	}
</script>
