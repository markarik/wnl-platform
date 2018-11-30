<template>
	<div>
		<user-edit-form :populate="true" :resource-url="resourceUrl" method="put"/>
	</div>
</template>

<script>
	import UserEditForm from "./UserEditForm"


	export default {
		components: {UserEditForm},
		props: {
			user: {
				type: Object,
				required: true
			}
		},
		computed: {
			resourceUrl() {
				return `users/${this.user.id}?include=roles`
			}
		},
	}
</script>
