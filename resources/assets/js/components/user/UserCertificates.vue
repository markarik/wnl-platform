<template>
	<div class="scrollable-main-container wnl-user-profile" :class="{mobile: isMobileProfile}">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Certyfikaty Do Pobrania
				</div>
			</div>
		</div>
		<ul v-if="participationCertificates.length">
			<li v-for="order in participationCertificates" :key="order.id">
				<a @click="downloadParticipationCertificate(order.id)">
					Certyfikat Uczestnictwa: {{order.product.name}} - {{formatDate(order.product.course_start)}} - {{formatDate(order.product.course_end)}}
				</a>
			</li>
		</ul>
		<ul v-if="finalCertificates.length">
			<li v-for="order in finalCertificates" :key="order.id">
				<a @click="downloadFinalCertificate(order.id)">
					Certyfikat Ukończenia: {{order.product.name}} - {{formatDate(order.product.course_start)}} - {{formatDate(order.product.course_end)}}
				</a>
			</li>
		</ul>
		<div v-else>
			<div class="box has-text-centered">
				<p class="title is-5">Brak certyfikatów do pobrania</p>
			</div>
		</div>

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
				participationCertificates: [],
				finalCertificates: []
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
					const response = await axios.request({
						url: getApiUrl(`certificates/participation/${orderId}`),
						responseType: 'blob',
					})

					const data = window.URL.createObjectURL(response.data);
					const link = document.createElement('a')
					link.style.display = 'none';
					// For Firefox it is necessary to insert the link into body
					document.body.appendChild(link);
					link.href = data
					link.setAttribute('download', `${orderId}.jpg`)
					link.click()

					setTimeout(function() {
						// For Firefox it is necessary to delay revoking the ObjectURL
						window.URL.revokeObjectURL(link.href)
						document.removeChild(link);
					}, 100)
				} catch (err) {
					if (err.response.status === 404) {
						return this.addAutoDismissableAlert({
							text: 'Nie udało się znaleźć certyfikatu. Spróbuj ponownie, jeśli problem nie ustąpi daj Nam znać :)',
							type: 'error'
						})
					}

					if (err.response.status === 403) {
						return this.addAutoDismissableAlert({
							text: 'Nie masz uprawnień do pobrania certyfikatu.',
							type: 'error'
						})
					}

					this.addAutoDismissableAlert({
						text: 'Ups, coś poszło nie tak, spróbuj ponownie.',
						type: 'error'
					})

					$wnl.logger.capture(err)
				}
			},
			async downloadFinalCertificate(orderId) {
				try {
					const response = await axios.request({
						url: getApiUrl(`certificates/final/${orderId}`),
						responseType: 'blob',
					})

					const data = window.URL.createObjectURL(response.data);
					const link = document.createElement('a')
					link.style.display = 'none';
					// For Firefox it is necessary to insert the link into body
					document.body.appendChild(link);
					link.href = data
					link.setAttribute('download', `${orderId}.jpg`)
					link.click()

					setTimeout(function() {
						// For Firefox it is necessary to delay revoking the ObjectURL
						window.URL.revokeObjectURL(link.href)
						document.removeChild(link);
					}, 100)
				} catch (err) {
					if (err.response.status === 404) {
						return this.addAutoDismissableAlert({
							text: 'Nie udało się znaleźć certyfikatu. Spróbuj ponownie, jeśli problem nie ustąpi daj Nam znać :)',
							type: 'error'
						})
					}

					if (err.response.status === 403) {
						return this.addAutoDismissableAlert({
							text: 'Nie masz uprawnień do pobrania certyfikatu.',
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
			const {data} = await axios.get(getApiUrl('certificates'));

			this.participationCertificates = Object.values(data.participation);
			this.finalCertificates = Object.values(data.final);
		}
	}
</script>
