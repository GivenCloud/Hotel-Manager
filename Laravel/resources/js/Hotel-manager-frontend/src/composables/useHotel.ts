import { type Hotel } from "../interfaces/Hotel/hotel.interface"
import { onMounted, ref } from "vue"
import axios from "axios"


export const useHotel = () => {

    const hotels = ref<Hotel[]>([])

    const fetchHotels = async () => {
      try {
        const response = await axios.get('http://hotel-manager.test/api/hotel'); // URL de la API
        hotels.value = response.data;
      } catch (error) {
        console.error('Error fetching hotels:', error);
      }
    };
    
      onMounted(() => {
        fetchHotels();
      });
    
      return {
        hotels
      };
}

//(await appApi.get('/hotel')).data