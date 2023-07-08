<template>
    <Header />
    <div class="pt-16 mt-16">
        <div class="overflow-hidden sm:rounded-md max-w-sm mx-auto text-left">
            <div v-if="pharmacyDetail && !order.id" class="flex flex-col justify-center items-center mb-4">
                <div class="mb-4">
                    <img class="rounded-full w-40 h-40" :src="`http://127.0.0.1:8000/uploads/${pharmacyDetail.pharmacy.logo}`" alt="PharmaFast">
                </div>
                <h1 class="text-3xl font-semibold">{{ pharmacyDetail.pharmacy.pharmacy_name }}</h1>
                <p>{{ pharmacyDetail.pharmacy.contact_person }}</p>
                <p>{{ pharmacyDetail.pharmacy.phone_no }}</p>
            </div>
            <div class="font-semibold p-4 mb-4 text-center text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                {{ title }}
            </div>
            <div v-if="!order.id" class="mt-8 flex justify-center">
                <Loader />
            </div>
            <div v-else>
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div class="mt-2">
                        <p class="text-lg">Requested by <strong>{{ order.user.name }}</strong></p>
                    </div>
                    <div class="mb-4">
                        <p class="text-lg">Description</p>
                        <p>{{ order.description }}</p>
                    </div>
                    <div>
                        <img :src="`http://127.0.0.1:8000/uploads/${order.prescription_file}`"
                            class="max-w-sm rounded border bg-white p-1 dark:border-neutral-700 dark:bg-neutral-800 h-60" />
                    </div>
                </div>
                <div class="flex justify-between bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button
                        @click="handleDeclineOrder"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">Decline</button>
                    <button
                        @click="handleAcceptOrder"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Accept</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref } from 'vue'
import Loader from '@/components/Loader.vue'
import { onMounted } from 'vue'
import Echo from 'laravel-echo'
import { useOrderStore } from '@/stores/order'
import http from '@/helpers/http'
import Header from '@/components/Header.vue'
import Pusher from 'pusher-js'

const title = ref('Waiting for medicine request...')
const pharmacyDetail = ref(null)
const order = useOrderStore()

const handleDeclineOrder = () => {
    order.reset()
    title.value = 'Waiting for medicine request...'
}

const handlePharmacyDetail = () => {
    http().get(`/api/pharmacy`)
    .then((response) => {
        pharmacyDetail.value = response.data
    })
    .catch(() => {
    })
}

const handleAcceptOrder = () => {
    http().post(`/api/order/${order.id}/accept`, {})
    .then(() => {
        order.reset()
        title.value = 'Waiting for medicine request...'
    })
    .catch(() => {
    })
}

onMounted(async () => {
    handlePharmacyDetail();

    let echo = new Echo({
        broadcaster: 'pusher',
        key: 'mykey',
        cluster: 'mt1',
        wsHost: window.location.hostname,
        wsPort: 6001,
        forceTLS: false,
        disableStats: true,
        enabledTransports: ['ws', 'wss']
    })

    echo.channel('pharmacies')
    .listen('OrderCreated', (e) => {
        title.value = 'A new medicine requested!'

        order.$patch(e.order)
    })
})
</script>