import { ref, computed, reactive } from 'vue'
import { defineStore } from 'pinia'

export const useOrderStore = defineStore('order', () => {
    const id = ref(null)
    const user_id = ref(null)

    const prescription_file = ref(null)
    const description = ref(null)
    const is_accepted = ref(false)

    const user = reactive({
        name: null,
        phone: null
    })

    const pharmacy = reactive({
        id: null,
        year: null,
        make: null,
        model: null,
        license_plate: null,
        user: {
            name: null,
        },
        pharmacy_name: null,
        address: null,
        logo: null,
        contact_person: null,
        phone_no: null
    })

    const reset = () => {
        id.value = null
        user_id.value = null

        prescription_file.value = null
        description.value = null
        
        user.name = null
        user.phone = null

        pharmacy.id = null
        pharmacy.user.name = null

        is_accepted.value = false
    }

    return { id, user_id, prescription_file, description, user, pharmacy, is_accepted, reset}
})