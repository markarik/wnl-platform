<template>
	<div class="scrollable-main-container wnl-user-profile" :class="{mobile: isMobileProfile}">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<h3 class="level-item big strong title is-3">
					Certyfikaty Do Pobrania
				</h3>
			</div>
		</div>
		<template v-if="participationCertificates.length || finalCertificates.length">
			<div class="level-left big strong">
				Certyfikaty Uczestnictwa
			</div>
			<ul>
				<li v-for="order in participationCertificates" :key="order.id">
					<a @click="downloadParticipationCertificate(order.id)">
						Certyfikat Uczestnictwa: {{order.product.name}} - {{formatDate(order.product.course_start)}} - {{formatDate(order.product.course_end)}}
					</a>
				</li>
			</ul>

			<div class="level-left big strong margin top">
				Certyfikaty Ukończenia
			</div>
			<ul>
				<li v-for="order in finalCertificates" :key="order.id">
					<a @click="downloadFinalCertificate(order.id)">
						Certyfikat Ukończenia: {{order.product.name}} - {{formatDate(order.product.course_start)}} - {{formatDate(order.product.course_end)}}
					</a>
				</li>
			</ul>
		</template>
		<div v-else>
			<div class="box has-text-centered">
				<p class="title is-5">Brak certyfikatów do pobrania</p>
			</div>
		</div>

	</div>
</template>
<script>
import moment from 'moment';
import { mapActions, mapGetters } from 'vuex';
import axios from 'axios';
import { getApiUrl } from 'js/utils/env';
import { downloadFile } from 'js/utils/download';

export default {
	name: 'UserCertificates',
	data() {
		return {
			participationCertificates: [],
			finalCertificates: []
		};
	},
	computed: {
		...mapGetters(['isMobileProfile']),
	},
	async mounted() {
		const { data } = await axios.get(getApiUrl('certificates'));

		this.participationCertificates = Object.values(data.participation);
		this.finalCertificates = Object.values(data.final);
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		formatDate(date) {
			return moment(date).format('DD/MM/YY');
		},
		async downloadParticipationCertificate(orderId) {
			try {
				const response = await axios.request({
					url: getApiUrl(`certificates/participation/${orderId}`),
					responseType: 'blob',
				});

				downloadFile(response.data, `participation_${orderId}.jpg`);
			} catch (err) {
				this.handleDownloadFailure(err);
			}
		},
		async downloadFinalCertificate(orderId) {
			try {
				const response = await axios.request({
					url: getApiUrl(`certificates/final/${orderId}`),
					responseType: 'blob',
				});

				downloadFile(response.data, `final_${orderId}.jpg`);
			} catch (err) {
				this.handleDownloadFailure(err);
			}
		},
		handleDownloadFailure(err) {
			this.addAutoDismissableAlert({
				text: 'Ups, coś poszło nie tak, spróbuj ponownie.',
				type: 'error'
			});

			$wnl.logger.capture(err);
		},
	},
};
</script>
