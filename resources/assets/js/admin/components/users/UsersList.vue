<template>
	<div class="annotations-list">
		<slot name="search"></slot>
		<slot name="pagination"/>
		<ul v-if="list.length">
			<table class="users-list">
				<tbody>
					<tr v-for="(user, index) in list" class="users-list__item">
						<td>{{ user.id }}</td>
						<td>{{ user.full_name }}</td>
					</tr>
				</tbody>
			</table>
		</ul>
		<div v-else>
			<span class="title is-6">Nic tu nie ma...</span>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.users-list
		&__item
			&:nth-child(even)
				background: $color-background-light-gray
			td
				padding: 5px
</style>

<script>
	export default {
		name: 'AnnotationsList',
		data() {
			return {
				openAnnotations: []
			}
		},
		props: {
			list: {
				type: Array,
				required: true
			},
			modifiedAnnotationId: {
				type: Number,
				default: 0
			}
		},
		methods: {

		}
	}
</script>
