<template>
	<div>
		<img
			class="splash-screen-image"
			:src="logoImageUrl"
			alt="Logo kursu"
		>
		<div class="has-text-centered">
			<p class="title is-4">Twoje zamówienie oczekuje na płatność</p>
			<p class="margin vertical">
				Na płatność masz <strong>7 dni</strong> od złożenia zamówienia.
			</p>
			<p class="margin vertical text-dimmed" v-if="currentProductAccessStartDateIsPast">Dostęp do kursu otrzymasz od razu po dokonaniu płatności.</p>
			<p class="margin vertical text-dimmed" v-else-if="currentProductAccessStartDate">Dostęp do kursu otrzymasz od {{currentProductAccessStartDate}}.</p>
			<p class="margin vertical">
				<router-link :to="{name: 'my-orders'}" class="button is-primary">
					Opłać zamówienie
				</router-link>
			</p>
			<!-- TODO PLAT-1201 clean up and do it correctly -->
			<p class="splash-screen__info text-dimmed" v-if="courseSlug === 'ldek'">
				Album map myśli wyślemy do Ciebie w 2. połowie maja.
			</p>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.splash-screen-image
		max-width: 240px
</style>

<script>
import moment from 'moment';
import { mapGetters } from 'vuex';

export default {
	computed: {
		...mapGetters('course', ['courseSlug']),
		...mapGetters('products', ['getCurrentCourse']),
		logoImageUrl() {
			return window.$wnl.course.productLogoWithStudents;
		},
		currentProductAccessStartDateIsPast() {
			return this.getCurrentCourse && moment(this.getCurrentCourse.access_start * 1000).isBefore();
		},
		currentProductAccessStartDate() {
			return this.getCurrentCourse && moment(this.getCurrentCourse.access_start * 1000).format('LL');
		},
	},
};
</script>
