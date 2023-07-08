<template>
    <Header />
    <div class="pt-16 mt-16">
        <h1 class="text-2xl font-semibold mb-4">What are you looking for today?</h1>
        <form action="#" @submit.prevent="handleRequest">
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium" for="file_input">Upload Prescription</label>
                        <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" aria-describedby="file_input_help" id="file_input" type="file" required v-on:change="handlePrescriptionFileChanged">
                        <p class="mt-1 text-sm dark:text-gray-700" id="file_input_help">PNG, JPG or GIF</p>
                    </div>
                    <div class="mt-2">
                        <textarea 
                            v-model="request.description"
                            name="description" id="description" 
                            placeholder="Description"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-700 shadow-sm focus:border-black focus:outline-none" required>
                        </textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button
                        type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Find
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
<script setup>
import { useRouter } from 'vue-router'
import Header from '@/components/Header.vue'
import { reactive } from 'vue'
import http from '@/helpers/http'
import { useOrderStore } from '@/stores/order'

const order = useOrderStore()

const request = reactive({
    prescriptionFile: null,
    description: '',
})

const router = useRouter()

const handlePrescriptionFileChanged = (file) => {
    if (file.length === 0) {
        return
    };

    request.prescriptionFile = file.target.files[0];
}

const handleRequest = () => {
    let settings = { headers: { 'content-type': 'multipart/form-data' } }

    let formData = new FormData();
    formData.append('file', request.prescriptionFile);
    formData.append('description', request.description);
    formData.append('_method', 'POST');
    http().post('/api/order', formData, settings)
    .then((response) => {
        order.$patch(response.data)
        router.push({
            name: 'order'
        })
    })
    .catch((error) => {
        console.error(error)
    })
}
</script>