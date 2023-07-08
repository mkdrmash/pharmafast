<template>
    <Header />
    <div class="pt-16 mt-16">
        <h1 class="text-3xl font-semibold mb-4">Fill in your pharmacy details</h1>
        <form action="#" @submit.prevent="handleSavePharmacy">
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div class="mt-2 mb-2">
                        <label class="block mb-2 text-sm font-medium" for="logo">Logo</label>
                        <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" aria-describedby="file_input_help" id="logo" type="file" name="logo" required v-on:change="handleLogoChanged">
                        <p class="mt-1 text-sm dark:text-gray-700" id="file_input_help">PNG, JPG or GIF</p>
                    </div>
                    <div class="mt-2">
                        <input type="text" name="pharmacyName" id="name" v-model="pharmacyDetails.pharmacyName" placeholder="Pharmacy name"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none" required>
                    </div>
                    <div class="mt-2">
                        <GMapAutocomplete
                            placeholder="Address"
                            @place_changed="handleLocationChanged"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none" required>
                        </GMapAutocomplete>
                    </div>
                    <div class="mt-2">
                        <input type="text" name="contactPerson" id="contactPerson" v-model="pharmacyDetails.contactPerson" placeholder="Contact Person"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none" required>
                    </div>
                    <div class="mt-2">
                        <input type="text" name="phoneNo" id="phoneNo" v-model="pharmacyDetails.phoneNo" placeholder="Phone Number"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none" required>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Continue
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
<script setup>
import { useLocationStore } from '@/stores/location'
import { reactive } from 'vue'
import http from '@/helpers/http'
import router from '../router';
import Header from '@/components/Header.vue'

const location = useLocationStore()

const pharmacyDetails = reactive({
    logo: null,
    pharmacyName: '',
    address: null,
    contactPerson: '',
    phoneNo: '',
})

const handleLogoChanged = (file) => {
    if (file.length === 0) {
        return
    };

    pharmacyDetails.logo = file.target.files[0];
}

const handleLocationChanged = (e) => {
    location.$patch({
        destination: {
            name: e.name,
            address: e.formatted_address,
            geometry: {
                lat: e.geometry.location.lat(),
                lng: e.geometry.location.lng()
            }
        }
    })
}

const handleSavePharmacy = () => {
    let settings = { headers: { 'content-type': 'multipart/form-data' } }

    let formData = new FormData();
    formData.append('file', pharmacyDetails.logo);
    formData.append('pharmacyName', pharmacyDetails.pharmacyName);
    formData.append('address', JSON.stringify({
        latLong: location.destination.geometry,
        placeName: location.destination.name
    }));
    formData.append('contactPerson', pharmacyDetails.contactPerson);
    formData.append('phoneNo', pharmacyDetails.phoneNo);
    formData.append('_method', 'POST');
    http().post('/api/pharmacy', formData, settings)
    .then(() => {
        router.push({
            name: 'standby'
        })
    })
    .catch((error) => {
        console.error(error)
    })
}
</script>