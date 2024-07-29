import axios from 'axios';
import { type Hotel } from '../types/hotel';

const API_URL = "http://hotel-manager.test/api/hotel";

export class useHotel {
  public async getHotels(): Promise<Hotel[]> {
    try {
      const response = await axios.get(API_URL);
      return response.data as Hotel[];
    } catch (error) {
      console.error('Error fetching hotels:', error);
      throw error;
    }
  }
}
