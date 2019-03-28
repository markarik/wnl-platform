<template>
	<div class="card order" :data-order-id="order.id">
		<div class="card-content">
			<div class="media">
				<div class="media-left">
					<figure class="product-logo image is-64x64">
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
			<div class="level">
				<div class="level-left">
					<div class="tags">
						<span :class="{'is-success': order.paid, 'tag': true }">{{ paymentStatus }}</span>
						<span class="tag">Metoda płatności: {{ paymentMethod }}</span>
						<slot name="order-tags"></slot>
					</div>
				</div>
				<div class="level-right"></div>
			</div>
			<div v-if="!order.canceled">
				<!-- COUPONS BEGINS -->
				<p v-if="coupon">
					<strong>Naliczona zniżka: "{{ coupon.name }}" o wartości {{ getCouponValue(coupon) }}</strong><br>
					Cena ze zniżką: {{ order.total }}zł
				</p>

				<!-- STUDY BUDDY BEGINS -->
				<div class="box margin bottom" v-else-if="studyBuddy && order.paid">
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
						z Twoim unikalnym kodem. <strong>Gdy opłaci zamówienie</strong> - zniżka zostanie naliczona także Tobie, a my wykonamy w ciągu 14 dni zwrot na konto, z którego opłacony został kurs!&nbsp;😉
						<p class="metadata aligncenter">Twój unikalny kod:</p>
						<span class="code">{{order.studyBuddy.code}}</span>
						<p class="small margin vertical has-text-centered">
							Dla ułatwienia, możesz po prostu wysłać jej ten link:
							<a :href="voucherUrl(order.studyBuddy.code)" target="_blank">
								{{voucherUrl(order.studyBuddy.code)}}
							</a>
						</p>
					</div>
					<!-- <a :href="voucherUrl(order.studyBuddy.code)">{{ order.studyBuddy.code }}</a> -->
				</div>
				<div v-else-if="!order.coupon && studyBuddy" class="box has-text-centered">
					<p>Po opłaceniu zamówienia lub pierwszej raty, w tym miejscu pojawi się Twój unikalny kod <strong>Study Buddy</strong>!</p>
					<p>Przekaż go znajomej osobie, aby zapisała się na kurs ze 100zł zniżką! <strong>Gdy opłaci zamówienie</strong> - zniżka zostanie naliczona także Tobie, a my wykonamy zwrot na konto, z którego opłacony został kurs!&nbsp;😉</p>
				</div>

				<!-- STUDY BUDDY ENDS -->

				<div class="current-payment">

					<!-- PAY ORDER BEGINS -->
					<div class="margin top aligncenter" v-if="!isPending && !order.paid && order.method === 'online' && order.total > 0">
						<p>
							<button
								:class="{
									'button': true,
									'is-primary': true,
									'is-loading': this.paymentLoading
								}"
								@click="pay">
								Opłać zamówienie
							</button>
						</p>
						<p class="metadata aligncenter margin top">Kwota do zapłaty: {{this.order.total}}zł</p>
					</div>
					<!-- PAY ORDER ENDS -->

					<!-- Instalments -->
					<div class="payment-details" v-if="!isFullyPaid">
						<p class="big strong" v-if="order.method === 'transfer'">
							Kwota: {{ order.total }}zł
						</p>
						<div v-if="order.method === 'instalments'">

							<p class="aligncenter margin top">
								<button
									data-button="pay-next-instalment"
									:class="{
									'button': true,
									'is-primary': true,
									'is-loading': this.paymentLoading
									}"
									@click="pay">
									Zapłać kolejną ratę
								</button>
							</p>
							<p class="metadata aligncenter margin vertical">
								Kolejna rata: <strong>{{ order.instalments.nextPayment.left_amount }}zł do
								{{ instalmentDate(order.instalments.nextPayment.due_date) }}</strong>
							</p>

							<table class="table is-striped">
								<tr>
									<th>Rata</th>
									<th>Termin płatności</th>
									<th>Zapłacone / Do&nbsp;zapłaty</th>
								</tr>
								<tr v-for="(instalment, index) in order.instalments.instalments" :key="instalment.id">
									<td>{{index + 1}}</td>
									<td>
										{{ instalmentDate(instalment.due_date) }}
									</td>
									<td class="instalment-amount" :data-instalment="index + 1">
										{{instalment.amount - instalment.left_amount}}zł / {{instalment.amount}}zł
									</td>
								</tr>
								<tr>
									<td>Razem</td>
									<td></td>
									<td>{{ order.total }}zł</td>
								</tr>
							</table>

							<!-- Transfer details -->
							<div class="transfer-details notification" v-if="transferDetails">
								<p>Dane do przelewu</p>
								<small>
									<p class="big">Tytuł przelewu:</p>
									<strong>Zamówienie numer {{ order.id }}</strong><br>
									bethink sp. z o.o.<br>
									ul. Henryka Sienkiewicza 8/1<br>
									60-817, Poznań<br>
									82 1020 4027 0000 1102 1400 9197 (PKO BP)
								</small>
							</div>
						</div>
					</div>

				</div>

				<!-- TABS BEGIN -->
				<div class="tabs">
					<ul>
						<li :class="{'is-active': activeTab === tab}"
							v-for="(tabContent, tab) in orderTabs"
							:key="tab"
							@click="activeTab = tab"
						>
							<a>
								<span class="icon is-small">
									<i :class="`fa fa-${tabContent.icon}`"></i>
								</span>
								{{ tabContent.text }}
							</a>
						</li>
					</ul>
				</div>

				<!-- PAYMENTS BEGIN -->
				<div class="content" v-if="activeTab === 'payments'">
					<div v-if="!order.payments.length" class="margin vertical">
						Brak płatności
					</div>
					<div v-else class="payments">
						<span class="payments__title">Historia płatności</span>
						<template v-if="canRetryPayment">
							<a class="payments__retry-link" @click="pay">Powtórz płatność</a>
						</template>
						<ul class="payments__list">
							<li v-for="payment in order.payments" :key="payment.id" class="payments__link">
								<span>{{formatTime(payment.created_at)}}</span> - <span :class="`payment--${payment.status}`">{{$t(`orders.status['${payment.status}']`)}}</span>
							</li>
						</ul>
						<small v-if="isPending">Księgowanie wpłat może potrwać do 3 dni roboczych.</small>
					</div>
				</div>
				<!-- PAYMENTS END -->

				<!-- INVOICES BEGIN -->
				<div class="content" v-if="activeTab === 'invoices'">
					<div v-if="!order.invoices.length" class="margin vertical">
						Brak faktur
					</div>
					<div v-else class="invoices">
						<p class="metadata">Kliknij, aby pobrać faktury:</p>
						<ul>
							<li v-for="invoice in order.invoices" :key="invoice.id" class="invoices__link">
								<a @click="downloadInvoice(invoice)">{{invoice.number}}</a>
							</li>
						</ul>
					</div>
				</div>
				<!-- INVOICES END -->

				<!-- COUPONS BEGIN -->
				<div class="content" v-if="activeTab === 'coupons'">
					<template v-if="couponsDisabled">
						<p>{{$t('orders.messages.product-coupons-disabled')}}</p>
					</template>
					<div class="add-coupon" v-else>
						<a class=""
							title="Dodaj lub zmień kod rabatowy"
							@click="toggleCouponInput"
							v-if="order.status !== 'closed'">
							<span class="icon is-small margin right"><i class="fa fa-plus"></i></span>
							<span>Dodaj lub zmień kod rabatowy</span>
						</a>
					</div>
					<div class="voucher-code" v-if="couponInputVisible">
						<wnl-form class="margin vertical"
									name="CouponCode"
									method="put"
									:resource-route="couponUrl"
									hide-default-submit="true"
									@submitSuccess="couponSubmitSuccess">
							<wnl-form-text name="code" placeholder="XXXXXXXX">Wpisz kod:</wnl-form-text>
							<wnl-submit>Wykorzystaj kod</wnl-submit>
						</wnl-form>
					</div>
				</div>
				<!-- COUPONS END -->
				<!-- TABS END -->
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
			<div class="card-footer-item cancel-order" v-if="!order.paid && !order.canceled && order.total > 0">
				<a title="Anuluj zamówienie" @click="cancelOrder">
					<span class="icon is-small status-icon">
						<i class="fa fa-times"></i>
					</span> {{ $t('orders.cancel.button') }}
				</a>
			</div>
		</div>

		<wnl-p24-form
				:user-data="userData"
				:payment-data="paymentData"
				:product-name="order.product.name"
				ref="p24Form"
		/>
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
			color: $color-darker-gray

	.cancel-order
		a,
		a:visited
			color: $color-red

		a:hover
			color: $color-orange

	.current-payment
		padding: $margin-base 0 $margin-big

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

	.payments
		display: flex
		flex-wrap: wrap

		&__list
			flex-basis: 100%

		&__title
			flex: 1 0 auto
	.payment
		&--in-progress
			color: $warning
		&--error
			color: $danger
		&--success
			color: $success

	.next-payment
		align-items: center
		display: flex
		flex-direction: row
		justify-content: space-between

</style>

<script>
import moment from 'moment';
import axios from 'axios';
import {mapActions, mapGetters, mapMutations} from 'vuex';
import {getUrl, getApiUrl, getImageUrl} from 'js/utils/env';
import {gaEvent} from 'js/utils/tracking';
import {Form, Text, Submit} from 'js/components/global/form';
import P24Form from 'js/components/user/P24Form';
import { swalConfig } from 'js/utils/swal';
import {nextTick} from 'vue';
import * as types from 'js/store/mutations-types';

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
			activeTab: 'payments',
			code: '',
			couponInputVisible: false,
			order: this.orderInstance,
			paymentData: {},
			paymentMethods: {
				'free': 'Darmowe uczestnictwo',
				'transfer': 'Tradycyjny przelew',
				'online': 'Przelewy24 - Pełna kwota',
				'instalments': 'Przelewy24 - Raty',
			},
			paymentLoading: false,
			orderTabs: {
				'payments': {
					icon: 'money',
					text: 'Płatności',
				},
				'invoices': {
					icon: 'file-o',
					text: 'Faktury'
				},
				'coupons': {
					icon: 'gift',
					text: 'Zniżki',
				},
			},
			userData: {},
		};
	},
	computed: {
		...mapGetters(['isAdmin']),
		couponsDisabled() {
			if (this.order.product.signups_end) {
				return new Date(this.order.product.signups_end * 1000) < new Date();
			}
			return true;
		},
		canRetryPayment() {
			if (!_.get(this.order, 'payments.length', 0)) {
				return !this.order.paid;
			}
			return !this.order.payments.find(payment => payment.status === 'success');
		},
		coupon() {
			return this.order.coupon;
		},
		logoUrl() {
			return getUrl($wnl.course.productLogo);
		},
		isFullyPaid() {
			return this.order.paid_amount >= this.order.total;
		},
		isPending() {
			// show loader only if there is an online payment waiting for confirmation
			const payments = _.get(this.order, 'payments', []);

			if (this.order.canceled) return false;
			if (payments.find(payment => payment.status === 'success')) return false;

			if (payments.find(payment => payment.status === 'in-progress')) return true;

			return false;
		},
		iconClass() {
			if (this.isPending) {
				// Loader
				return 'fa-circle-o-notch fa-spin';
			} else if (this.order.paid) {
				return 'fa-check-circle-o';
			}

			return 'fa-info-circle';
		},
		transferDetails() {
			return !this.isFullyPaid && this.order.method === 'transfer';
		},
		paymentDeadline() {
			return moment(this.order.created_at, 'DD-MM-YYYY').add(7, 'd').format('DD-MM-YYYY');
		},
		paymentStatus() {
			if (this.order.paid && !this.isPending || this.order.total === 0) {
				if (this.order.total === this.order.paid_amount) {
					return `Wpłacono ${this.order.paid_amount}zł / ${this.order.total}zł`;
				} else if (this.order.paid_amount > this.order.total) {
					return `Wpłacono ${this.order.paid_amount}zł, do zwrotu ${this.order.paid_amount - this.order.total}zł`;
				} else {
					return `Wpłacono ${this.order.paid_amount}zł`;
				}
			} else if (this.order.canceled) {
				return 'Anulowano';
			} else {
				return `Termin płatności: ${this.paymentDeadline}`;
			}
		},
		paymentStatusClass() {
			if (this.order.cancelled) {
				return 'text-warning';
			} else if (!this.isPending && this.order.paid && this.order.total <= this.order.paid_amount) {
				return 'text-success';
			}

			return 'text-info';
		},
		paymentMethod() {
			return this.paymentMethods[this.order.method];
		},
		canChangePaymentMethod() {
			return !this.order.paid && !this.order.canceled && this.order.total > 0;
		},
		paymentMethodChangeUrl() {
			return getUrl('payment/confirm-order');
		},
		orderNumber() {
			return `Zamówienie numer ${this.order.id}`;
		},
		studyBuddy() {
			return this.order.hasOwnProperty('studyBuddy') && this.order.studyBuddy.status !== 'expired';
		},
		couponUrl() {
			return `orders/${this.order.id}/coupon`;
		},
		amountToBePaidNext() {
			if (this.order.method === 'instalments') {
				return this.order.instalments.nextPayment.left_amount;
			}

			return this.order.total;
		}
	},
	methods: {
		...mapMutations({
			'setSubscription': types.USERS_SET_SUBSCRIPTION
		}),
		...mapActions(['addAutoDismissableAlert']),

		async downloadInvoice(invoice) {
			try {
				const response = await axios.request({
					url: getApiUrl(`invoices/${invoice.id}`),
					responseType: 'blob',
				});

				const data = window.URL.createObjectURL(response.data);
				const link = document.createElement('a');
				link.style.display = 'none';
				// For Firefox it is necessary to insert the link into body
				document.body.appendChild(link);
				link.href = data;
				link.setAttribute('download', `${invoice.id}.pdf`);
				link.click();

				setTimeout(function() {
					// For Firefox it is necessary to delay revoking the ObjectURL
					window.URL.revokeObjectURL(link.href);
					document.removeChild(link);
				}, 100);
			} catch(err) {
				if (err.response.status === 404) {
					return this.addAutoDismissableAlert({
						text: 'Nie udało się znaleźć faktury. Spróbuj ponownie, jeśli problem nie ustąpi daj Nam znać :)',
						type: 'error'
					});
				}

				if (err.response.status === 403) {
					return this.addAutoDismissableAlert({
						text: 'Nie masz uprawnień do pobrania tej faktury.',
						type: 'error'
					});
				}

				this.addAutoDismissableAlert({
					text: 'Ups, coś poszło nie tak, spróbuj ponownie.',
					type: 'error'
				});

				$wnl.logger.capture(err);
			}
		},
		checkStatus() {
			axios.get(getApiUrl(`orders/${this.order.id}?include=payments`))
				.then((response) => {
					const {included = {}, ...order} = response.data;
					const {payments = {}} = included;
					if (order.paid) {
						this.order.paid        = true;
						this.order.paid_amount = order.paid_amount;
						this.order.payments = (order.payments || []).map(paymentId => payments[paymentId]);

						axios.get(getApiUrl('user_subscription/current'))
							.then(response => {
								this.setSubscription(response.data);
							});
					} else {
						setTimeout(this.checkStatus, 10000);
					}
				})
				.catch(exception => $wnl.logger.capture(exception));
		},
		couponSubmitSuccess() {
			axios.get(getApiUrl(`orders/${this.order.id}`))
				.then(response => {
					this.order = {
						...this.order,
						...response.data
					};
					this.toggleCouponInput();
				})
				.catch(exception => $wnl.logger.capture(exception));
		},
		voucherUrl(code){
			return code ? getUrl(`payment/voucher?code=${code}`) : getUrl('payment/voucher');
		},
		instalmentDate(date) {
			return moment(date).format('LL');
		},
		getCouponValue(coupon) {
			return coupon.type === 'amount' ? `${coupon.value}zł` : `${coupon.value}%`;
		},
		toggleCouponInput(){
			this.couponInputVisible = !this.couponInputVisible;
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
						$wnl.logger.capture(error);
					}
				});
		},
		async pay() {
			this.paymentLoading = true;

			const [{data: paymentData}, {data: userData}] = await Promise.all([
				axios.post(getApiUrl('payments'), {
					    order_id: this.order.id,
					amount: this.amountToBePaidNext
				}),
				axios.get(getApiUrl('users/current/address'))
			]);
			this.paymentData = paymentData;
			this.userData = userData;

			nextTick(() => {
				this.$refs.p24Form.$el.submit();
			});
		},
		formatTime(time) {
			return moment(time * 1000).format('L LT');
		}
	},
	mounted() {
		if (this.isPending) this.checkStatus();
		if (this.$route.query.hasOwnProperty('payment')) {
			gaEvent('Payment', this.order.method);
		}
	}
};
</script>
