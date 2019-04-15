<template>
	<wnl-user-address :address="user.user_address" :resource-route="`users/${user.id}/address`" />
</template>

<script>
import WnlUserAddress from 'js/components/user/UserAddress';

export default {
	name: 'UserAddress',
	components: { WnlUserAddress },
	props: ['user']
};
</script>

<style scoped>

</style>