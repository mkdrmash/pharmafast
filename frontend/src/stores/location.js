import { reactive } from 'vue'
import { defineStore } from 'pinia'

export const useLocationStore = defineStore('location', () => {
    const destination = reactive({
        name: '',
        address: '',
        geometry: {
            lat: null,
            lng: null
        }
    })

    const reset = () => {
        destination.name = ''
        destination.address = ''
        destination.geometry.lat = null
        destination.geometry.lng = null
    }
    
    return { destination, reset }
})
