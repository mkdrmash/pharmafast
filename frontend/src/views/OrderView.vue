<template>
    <Header />
    <div class="pt-16 mt-16">
        <div class="overflow-hidden sm:rounded-md max-w-md mx-auto">
            <div v-if="order.is_accepted" class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                {{ title }}
            </div>
            <div v-else class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                {{ title }}
            </div>
            <div v-if="order.is_accepted">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div v-if="order.pharmacy" class="text-left">
                        <div class="flex flex-row justify-start items-center">
                            <div class="mr-4">
                                <img class="rounded-full w-20 h-20" :src="`http://127.0.0.1:8000/uploads/${order.pharmacy.logo}`" alt="image description">
                            </div>
                            <div>
                                <p><strong>{{ order.pharmacy.pharmacy_name }}</strong></p>
                                <p>{{ order.pharmacy.contact_person }}</p>    
                                <p>{{ order.pharmacy.phone_no }}</p>    
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-5 text-right sm:px-6">
                    <a class="py-2.5 px-5 mr-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                        :href="`tel:${order.pharmacy.phone_no}`">Call</a>
                    <a class="py-2.5 px-5 mr-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                        :href="`sms:${order.pharmacy.phone_no}`">SMS</a>
                    <a class="py-2.5 px-5 mr-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                        :href="`https://www.google.com/maps/search/?api=1&query=${locationView.lat},${locationView.lng}`" target="_blank">
                        Location
                    </a>
                    <button 
                        @click="handleCompleteOrder"
                        type="button" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Complete</button>
                </div>
            </div>
            <div v-else>
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div class="flex flex-row justify-center">
                        <img src="/waiting.gif"
                            class="h-auto max-w-sm rounded-lg shadow-none transition-shadow duration-300 ease-in-out hover:shadow-lg hover:shadow-black/30"
                            alt="" />
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-left sm:px-6">
                    <span>{{ message }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { useOrderStore } from '@/stores/order'
import { onMounted, ref } from 'vue'
import Echo from 'laravel-echo'
import Header from '@/components/Header.vue'
import http from '@/helpers/http'
import { useRouter } from 'vue-router'

const router = useRouter()
const order = useOrderStore()

const title = ref('Waiting on a pharmacist replay...')
const message = ref('When a pharmacist accepts your request, their info will appear here.')

const locationView = ref({
    lat: null,
    lng: null
})

const handleCompleteOrder = () => {
    http().post(`/api/order/${order.id}/complete`, {})
    .then(() => {
        order.reset()
        router.push('home')
    })
    .catch(() => {
    })
}

onMounted(() => {
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

    echo.channel(`user_${order.user_id}`)
    .listen('OrderAccepted', (e) => {
        order.$patch(e.order)

        let location = JSON.parse(e.order.pharmacy.address)
        locationView.value.lat = location.latLong.lat
        locationView.value.lng = location.latLong.lng

        title.value = "A pharmacist has accepted your request!"
        message.value = `${e.order.pharmacy.contact_person} is coming `
    })
})
</script>