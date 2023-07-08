<template>
    <div class="pt-16">
        <div class="max-w-sm mx-auto mb-5">
            <div class="flex flex-col justify-center items-center">
                <div class="mb-4">
                    <img class="rounded-full w-40 h-40" src="/logo.jpeg" alt="PharmaFast">
                </div>
                <h1 class="text-3xl font-semibold mb-4">PharmaFast</h1>
            </div>
            <div v-if="alert.error" class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">{{ alert.title }}</span> {{ alert.message }}
            </div>
            <div v-if="alert.success" class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                <span class="font-medium">{{ alert.title }}</span> {{ alert.message }}
            </div>
            <div v-if="alert.warning" class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300" role="alert">
                <span class="font-medium">{{ alert.title }}</span> {{ alert.message }}
            </div>
        </div>
        <form v-if="!waitingOnVerification" action="#" @submit.prevent="handleLogin">
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <label for="phone" class="block text-sm font-medium leading-6 text-gray-900">Enter your phone number</label>
                        <input type="text" v-maska data-maska="##########" v-model="credentials.phone" name="phone" id="phone" placeholder="0911000000"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none" required>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button @submit.prevent="handleLogin" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Continue</button>
                </div>
            </div>
        </form>
        <form v-else action="#" @submit.prevent="handleVerification">
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <label for="login_code" class="block text-sm font-medium leading-6 text-gray-900">Enter your verification number</label>
                        <input type="text" v-maska data-maska="######" v-model="credentials.login_code" name="login_code" id="login_code" placeholder="123456"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none" required>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button @submit.prevent="handleVerification" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Verify</button>
                </div>
            </div>
        </form>
    </div>
</template>
<script setup>
import { vMaska } from 'maska'
import { reactive, ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router';

const router = useRouter()

const credentials = reactive({
    phone: null,
    login_code: null
})

const waitingOnVerification = ref(false)

const alert = ref({
    success: false,
    warning: false,
    error: false,
    title: "Error!",
    message: "Ops!, something went wrong"
})

onMounted(() => {
    if (localStorage.getItem('token')) {
        router.push({
            name: 'home'
        })
    }
})

const resetAlert = () => {
    alert.value = {
        success: false,
        warning: false,
        error: false,
        title: "Error!",
        message: "Ops!, something went wrong"
    }
}

const getFormattedCredentials = () => {
    return {
        phone: credentials.phone.replaceAll(' ', '').replace('(', '').replace(')', '').replace('-', ''),
        login_code: credentials.login_code
    }
}

const handleLogin = () => {
    if(!credentials.phone || credentials.phone === ""){
        resetAlert();
        alert.value.error = true;
        alert.value.title = "Error!";
        alert.value.message = "Phone number is required.";
        return;
    }

    axios.post('http://127.0.0.1:8000/api/login', getFormattedCredentials())
    .then((response) => {
        console.log(response.data)
        waitingOnVerification.value = true
    })
    .catch((error) => {
        resetAlert();
        alert.value.error = true;
        alert.value.title = "Error!";
        alert.value.message = error.response.data.message;
        
    })
}

const handleVerification = () => {
    axios.post('http://127.0.0.1:8000/api/login/verify', getFormattedCredentials())
        .then((response) => {
            localStorage.setItem('token', response.data.token)
            localStorage.setItem('username', response.data.username)
            router.push({
                name: 'home'
            })
        })
        .catch((error) => {
            resetAlert();
            alert.value.error = true;
            alert.value.title = "Error!";
            alert.value.message = error.response.data.message;
        })
}
</script>