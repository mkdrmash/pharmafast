<template>
    <Header />
    <div class="pt-16 mt-20">
        <h1 v-if="user && user !== '' && user !== 'null'" class="text-2xl font-semibold mb-4">Welcome! {{ user }}</h1>
        <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
            <div class="bg-white px-4 py-5 sm:p-6 text-center">
                <h1 class="text-2 font-semibold mb-4">What do you want to do today?</h1>
                <div class="flex flex-col justify-between">
                    <button 
                        @click="handleFindMedicine"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Find Medicine
                    </button>
                    <button 
                        @click="handlePharmacy"
                        class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                        Go to My Pharmacy
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { useRouter } from 'vue-router'
import http from '@/helpers/http'
import Header from '@/components/Header.vue'

const user = localStorage.getItem('username')
const router = useRouter()

const handlePharmacy = () => {
    http().get('/api/pharmacy')
    .then((response) => {
        if (response.data.pharmacy) {
            router.push({
                name: 'standby'
            })
        } else {
            router.push({
                name: 'pharmacy'
            })
        }
    })
    .catch((error) => {
        console.error(error)
    })
}

const handleFindMedicine = () => {
    http().get('/api/user')
    .then((response) => {
        if (!response.data.name) {
            router.push({
                name: 'user'
            })
        } else {
            router.push({
                name: 'find-medicine'
            })
        }
    })
    .catch((error) => {
    })
}
</script>