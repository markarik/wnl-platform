<template lang="html">
	<div class="card">
		<div class="card-content">
			<div class="media">
				<div class="media-left">
					<figure class="product-logo image is-48x48">
						<img :src="logoUrl" alt="Logo produktu">
					</figure>
				</div>
				<div class="media-content">
					<p class="title is-4">{{ order.product.name }}</p>
					<p class="subtitle is-6">{{ orderNumber }}
						<br>
						<small>Cena produktu: {{ order.product.price }}zł, zamówienie złożono {{ order.created_at }}
						</small>
					</p>
				</div>
			</div>
			<div class="content" v-if="!order.canceled">
				<p v-if="coupon">
					<strong>Naliczona zniżka: "{{ coupon.name }}" o wartości {{ getCouponValue(coupon) }}</strong><br>
					Cena ze zniżką: {{ order.total }}zł
				</p>

				<div class="margin bottom" v-else-if="studyBuddy && order.paid">
					<div v-if="order.studyBuddy.status === 'awaiting-refund'">
						<p class="strong has-text-centered">
							Twój Study Buddy dołączył już do kursu!
						</p>
						<p>
							Jeśli wysłałeś już do nas w odpowiedzi na maila dane do przelewu, w ciągu najbliższych dni
							otrzymasz zwrot.
							<wnl-emoji name="+1"/>
						</p>
						<p>
							Jeżeli nie, prosimy sprawdź swoją skrzynkę mailową. Znajdziesz tam wiadomość od nas o tytule
							"Twój Study Buddy dołączył właśnie do kursu! (Zamówienie {{order.id}})". W odpowiedzi wyślij
							nam dane do przelewu, których możemy użyć do zwrotu.
							<wnl-emoji name="wink"/>
						</p>
					</div>
					<div v-else>
						<p class="strong has-text-centered">
							Dziękujemy za opłacenie zamówienia! Możesz teraz skorzystać z promocji Study Buddy!
						</p>
						Znajdź jedną osobę, która po wejściu na <a :href="voucherUrl()">{{voucherUrl()}}</a> zapisze się
						z Twoim unikalnym kodem:
						<span class="code">{{order.studyBuddy.code}}</span>
						Obydwoje otrzymacie 100zł zniżki, <strong>jeżeli jej zamówienie zostanie opłacone</strong>!
						Przed zwrotem otrzymasz od nas maila o tytule "Twój Study Buddy dołączył właśnie do kursu!
						(Zamówienie {{order.id}})" z prośbą o przekazanie danych, na który wykonamy przelew ze zwrotem.
						<wnl-emoji name="wink"/>
						<p class="small margin vertical has-text-centered">
							Dla ułatwienia, możesz wysłać jej ten link: <a :href="voucherUrl(order.studyBuddy.code)"
																			 target="_blank">{{voucherUrl(order.studyBuddy.code)}}</a>
						</p>
					</div>
					<!-- <a :href="voucherUrl(order.studyBuddy.code)">{{ order.studyBuddy.code }}</a> -->
				</div>
				<p v-else-if="!order.coupon && studyBuddy" class="notification has-text-centered">
					Po opłaceniu zamówienia w tym miejscu pojawi się Twój unikalny kod, który możesz wysłać znajomym i
					skorzystać z promocji <strong>Study Buddy</strong> - gdy ktoś zapisze się używając Twojego kodu i
					opłaci zamówienie - obydwoje dostaniecie 100zł zniżki! Przed zwrotem napiszemy do Ciebie, aby
					uzyskać dane do przelewu.
					<wnl-emoji name="wink"/>
				</p>

				<p>Metoda płatności: {{ paymentMethod }}</p>

				<!-- Instalments -->
				<div class="payment-details" v-if="!isFullyPaid">
					<p class="big strong" v-if="order.method === 'transfer'">
						Kwota: {{ order.total }}zł
					</p>
					<div v-if="order.method === 'instalments'">
						<table class="table is-striped">
							<tr>
								<th>Rata</th>
								<th>Termin płatności</th>
								<th>Zapłacone / Do&nbsp;zapłaty</th>
							</tr>
							<tr v-for="(instalment, index) in order.instalments.instalments">
								<td>{{index + 1}}</td>
								<td>
									{{ instalmentDate(instalment.date) }}
								</td>
								<td>
									{{instalment.amount - instalment.left}}zł / {{instalment.amount}}zł
								</td>
							</tr>
							<tr>
								<td>Razem</td>
								<td></td>
								<td>{{ order.total }}zł</td>
							</tr>
						</table>
						<p class="next-payment margin bottom">
							Kolejna wpłata: <strong>{{ order.instalments.nextPayment.amount }}zł do
							{{ instalmentDate(order.instalments.nextPayment.date) }}</strong>
						</p>
					</div>
				</div>

				<!-- Transfer details -->
				<div class="transfer-details notification" v-if="transferDetails">
					<p>Dane do przelewu</p>
					<small>
						<strong>Zamówienie numer {{ order.id }}</strong><br>
						bethink sp. z o.o.<br>
						ul. Henryka Sienkiewicza 8/1<br>
						60-817, Poznań<br>
						82 1020 4027 0000 1102 1400 9197 (PKO BP)
					</small>
				</div>

				<div class="order-actions">
					<a title="Dodaj lub zmień kod rabatowy"
						@click="toggleCouponInput"
						v-if="order.status !== 'closed'">
						Dodaj lub zmień kod rabatowy
					</a>
					<a title="Anuluj zamówienie"
						@click="cancelOrder"
						v-if="!order.paid">
						{{ $t('orders.cancel.button') }}
					</a>
				</div>
				<div class="voucher-code" v-if="couponInputVisible">
					<wnl-form class="margin vertical"
								name="CouponCode"
								method="put"
								:resourceRoute="couponUrl"
								hideDefaultSubmit="true"
								@submitSuccess="couponSubmitSuccess">
						<wnl-form-text name="code" placeholder="XXXXXXXX">Wpisz kod:</wnl-form-text>
						<wnl-submit>Wykorzystaj kod</wnl-submit>
					</wnl-form>
				</div>
				<div v-if="order.invoices.length" class="invoices">
					<span class="invoices__title">Dokumenty do pobrania</span>
					<ul>
						<li v-for="invoice in order.invoices" :key="invoice.id" class="invoices__link">
							<a @click="downloadInvoice(invoice)">{{invoice.number}}</a>
						</li>
					</ul>
				</div>
				<div v-if="order.payments.length" class="payments">
					<span class="invoices__title">Historia Płatności</span>
					<button class="button" @click="retryPayment">Powtórz płatność</button>
					<wnl-p24-form
						:user-data="userData"
						:payment-data="paymentData"
						:productName="order.product.name"
						ref="p24Form"
					>
					</wnl-p24-form>
					<ul>
						<li v-for="payment in order.payments" :key="payment.id" class="invoices__link">
							<span>{{payment.created_at}}</span> - <span :class="`payment--${payment.status}`">{{$t(`orders.status['${payment.status}']`)}}</span>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="card-footer">
			<div class="card-footer-item payment-status" :class="paymentStatusClass">
				<span class="icon is-small status-icon">
					<i class="fa" :class="iconClass"></i>
				</span>
				{{ paymentStatus }}
			</div>
			<div class="card-footer-item payment-status" :class="paymentStatusClass" v-if="canChangePaymentMethod">
				<a :href="paymentMethodChangeUrl" title="Zmień metodę płatności">
					<span class="icon is-small status-icon">
						<i class="fa fa-pencil-square-o"></i>
					</span> Zmień metodę płatności
				</a>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/mixins'
	@import 'resources/assets/sass/variables'

	.card
		margin-bottom: 20px

	.product-logo
		+small-shadow()

	.status-icon
		line-height: $line-height-minus
		margin-right: 5px

	.payment-status
		line-height: $line-height-plus

		&.text-success
			color: $color-green

		&.text-warning
			color: $color-yellow

		&.text-info
			color: $color-gray-lighter

	.code
		background-color: $color-background-light-gray
		display: block
		font-size: $font-size-plus-2
		font-weight: $font-weight-bold
		letter-spacing: 1px
		margin: $margin-medium 0
		padding: $margin-small
		text-align: center

	.order-actions
		display: flex
		flex-direction: row
		justify-content: space-between

	.invoices, .payments
		margin-top: $margin-base

		&__link
			cursor: pointer

	.payment
		&--in-progress
			color: $warning
		&--error
			color: $danger
		&--success
			color: $success
</style>

<script>
	import moment from 'moment'
	import axios from 'axios'
	import {mapActions, mapGetters} from 'vuex'
	import {getUrl, getApiUrl, getImageUrl} from 'js/utils/env'
	import {gaEvent} from 'js/utils/tracking'
	import {Form, Text, Submit} from 'js/components/global/form'
	import P24Form from 'js/components/user/P24Form'
	import { swalConfig } from 'js/utils/swal'
	import {nextTick} from 'vue'

	export default {
		name: 'Order',
		props: ['orderInstance', 'loaderVisible'],
		components: {
			'wnl-form': Form,
			'wnl-form-text': Text,
			'wnl-submit': Submit,
			'wnl-p24-form': P24Form
		},
		data() {
			return {
				paymentMethods: {
					'free': '100% zniżki',
					'transfer': 'Przelew bankowy',
					'online': 'Szybki przelew',
					'instalments': 'Raty',
				},
				code: '',
				couponInputVisible: false,
				order: this.orderInstance,
				paymentData: {},
				userData: {},
			}
		},
		computed: {
			...mapGetters(['isAdmin', 'currentUser']),
			coupon() {
				return this.order.coupon
			},
			loaderSrc() {
				return getImageUrl('loader.svg')
			},
			logoUrl() {
				// TODO: Mar 28, 2017 - Make it dynamic when more courses are added
				return getImageUrl('wnl-logo-square@2x.png')
			},
			isPaid() {
				return this.order.paid
			},
			isFullyPaid() {
				return this.order.paid_amount >= this.order.total
			},
			isPending() {
				// show loader only if there is an online payment waiting for confirmation
				return !this.order.paid && this.order.method === 'online';
			},
			iconClass() {
				if (!this.order.paid && this.order.method === 'online') {
					// Loader
					return 'fa-circle-o-notch fa-spin'
				} else if (this.order.paid) {
					return 'fa-check-circle-o'
				}

				return 'fa-info-circle'
			},
			transferDetails() {
				return !this.isFullyPaid && (this.order.method === 'transfer' ||
						this.order.method === 'instalments')
			},
			paymentStatus() {
				if (this.order.paid) {
					if (this.order.total == this.order.paid_amount) {
						return `Zapłacono ${this.order.paid_amount}zł / ${this.order.total}zł`
					} else if (this.order.paid_amount > this.order.total) {
						return `Wpłacono ${this.order.paid_amount}zł, do zwrotu ${this.order.paid_amount - this.order.total}zł`
					} else {
						return `Wpłacono ${this.order.paid_amount}zł`
					}
				} else if (this.order.canceled) {
					return 'Anulowano'
				} else {
					return 'Oczekuje na zaksięgowanie (do 3 dni roboczych)'
				}
			},
			paymentStatusClass() {
				if (this.order.cancelled) {
					return 'text-warning'
				} else if (this.order.paid && this.order.total <= this.order.paid_amount) {
					return 'text-success'
				}

				return 'text-info'
			},
			paymentMethod() {
				return this.paymentMethods[this.order.method]
			},
			canChangePaymentMethod() {
				return !this.order.paid && !this.order.canceled
			},
			paymentMethodChangeUrl() {
				return getUrl('payment/confirm-order')
			},
			orderNumber() {
				return `Zamówienie numer ${this.order.id}`
			},
			studyBuddy() {
				return this.order.hasOwnProperty('studyBuddy') && this.order.studyBuddy.status !== 'expired'
			},
			couponUrl() {
				return `orders/${this.order.id}/coupon`;
			}
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),

			async downloadInvoice(invoice) {
				try {
					const response = await axios.request({
						url: getApiUrl(`invoices/${invoice.id}`),
						responseType: 'blob',
					})

					const data = window.URL.createObjectURL(response.data);
					const link = document.createElement('a')
					link.style.display = 'none';
					// For Firefox it is necessary to insert the link into body
					document.body.appendChild(link);
					link.href = data
					link.setAttribute('download', `${invoice.id}.pdf`)
					link.click()

					setTimeout(function() {
						// For Firefox it is necessary to delay revoking the ObjectURL
						window.URL.revokeObjectURL(link.href)
						document.removeChild(link);
					}, 100)
				} catch(err) {
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
			},
			checkStatus() {
				axios.get(getApiUrl(`orders/${this.order.id}`))
						.then((response) => {
							if (response.data.paid) {
								this.order.paid        = true
								this.order.paid_amount = response.data.paid_amount
							} else {
								setTimeout(this.checkStatus, 5000)
							}
						})
						.catch(exception => $wnl.logger.capture(exception))
			},
			couponSubmitSuccess() {
				axios.get(getApiUrl(`orders/${this.order.id}`))
						.then(response => {
							this.order = response.data
							this.toggleCouponInput()
						})
						.catch(exception => $wnl.logger.capture(exception))
			},
			voucherUrl(code){
				return code ? getUrl(`payment/voucher?code=${code}`) : getUrl('payment/voucher')
			},
			instalmentDate(date) {
				return moment(date.date).format('LL')
			},
			getCouponValue(coupon) {
				return coupon.type === 'amount' ? `${coupon.value}zł` : `${coupon.value}%`
			},
			toggleCouponInput(){
				this.couponInputVisible = !this.couponInputVisible
			},
			cancelOrder(){
				this.$swal(swalConfig({
					title: this.$t('orders.cancel.title'),
					text: this.$t('orders.cancel.text', {id: this.order.id}),
					showCancelButton: true,
					confirmButtonText: this.$t('ui.confirm.confirm'),
					cancelButtonText: this.$t('ui.confirm.cancel'),
					type: 'error',
					confirmButtonClass: 'button is-danger',
					reverseButtons: true
				}))
				.then(() => axios.get(getApiUrl(`orders/${this.order.id}/.cancel`)))
				.then(response => this.order = response.data)
				.catch(error => {
					if (error !== 'cancel') {
						$wnl.logger.capture(error)
					}
				})
			},
			async retryPayment() {
				const [{data: paymentData}, {data: userData}] = await Promise.all([
					axios.post(getApiUrl('payments'), {order_id: this.order.id}),
					axios.get(getApiUrl('users/current/address'))
				]);
				this.paymentData = paymentData;
				this.userData = userData;

				nextTick(() => {
					this.$refs.p24Form.$el.submit();
				})
			}
		},
		mounted() {
			if (this.isPending) this.checkStatus()
			if (this.$route.query.hasOwnProperty('payment')) {
				gaEvent('Payment', this.order.method)
			}
		}
	}
</script>
