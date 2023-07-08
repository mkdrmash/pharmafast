<template>
    <Header />
    <div class="pt-16 mt-16">
        <h1 class="text-3xl font-semibold mb-4">Tell us your name</h1>
        <form action="#" @submit.prevent="handleSavePharmacy">
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <input type="text" name="name" id="name" v-model="userDetails.name" placeholder="Your Name"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none">
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button @click="handleSavePharmacy"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Continue
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
<script setup>
import { reactive } from 'vue'
import http from '@/helpers/http'
import router from '../router';
import Header from '@/components/Header.vue'

const userDetails = reactive({
    name: ''
})
const handleSavePharmacy = () => {
    http().post('/api/user', userDetails)
    .then((response) => {
        localStorage.setItem('username', response.data.username)
        router.push({
            name: 'find-medicine'
        })
    })
    .catch((error) => {
        console.error(error)
    })
}
</script>