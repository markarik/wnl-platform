<template>
	<div class="scrollable-main-container wnl-user-profile" :class="{mobile: isMobileProfile}">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Certyfikaty Do Pobrania
				</div>
			</div>
		</div>
		<ul>
			<li v-for="order in orders" :key="order.id">
				<a @click="downloadParticipationCertificate(order.id)">
					Certyfikat Uczestnictwa: {{order.product.name}} - {{formatDate(order.product.course_start)}} - {{formatDate(order.product.course_end)}}
				</a>
			</li>
		</ul>

	</div>
</template>
<script>
	import moment from 'moment'
	import { mapActions, mapGetters } from 'vuex'
	import axios from 'axios'
	import {getApiUrl} from 'js/utils/env'

	export default {
		name: 'UserCertificates',
		data() {
			return {
				orders: []
			}
		},
		computed: {
			...mapGetters(['isMobileProfile']),
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			formatDate(date) {
				return moment(date).format('DD/MM/YY')
			},
			async downloadParticipationCertificate(orderId) {
				try {
					const response = await axios.get(getApiUrl(`certificates/participation/${orderId}`), {
						responseType: 'arraybuffer'
					})

					const blob = new Blob([response.data], { type: 'application/jpg' } )
					const link = document.createElement('a')
					link.href = window.URL.createObjectURL(blob)
					link.download = `${orderId}.jpg`
					link.click()
				} catch (err) {
					if (err.response.status === 404) {
						return this.addAutoDismissableAlert({
							text: 'Nie udało się znaleźć faktury. Spróbuj ponownie, jeśli problem nie ustąpi daj Nam znać :)',
							type: 'error'
						})
					}

					if (err.response.status === 403) {
						return this.addAutoDismissableAlert({
							text: 'Nie masz uprawnień do pobrania tej faktury.',
							type: 'error'
						})
					}

					this.addAutoDismissableAlert({
						text: 'Ups, coś poszło nie tak, spróbuj ponownie.',
						type: 'error'
					})

					$wnl.logger.capture(err)
				}
			}
		},
		async mounted() {
			const {data} = await axios.get(getApiUrl('certificates/participation'));

			this.orders = data.orders;
		}
	}
</script>
