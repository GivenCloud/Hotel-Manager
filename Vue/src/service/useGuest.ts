import axios from 'axios';
import { type Guest } from '../types/guest';

const API_URL = "http://hotel-manager.test/api/guest";

export class useGuest {
  public async getGuests(): Promise<Guest[]> {
    try {
      const response = await axios.get(API_URL);
      return response.data as Guest[];
    } catch (error) {
      console.error('Error fetching guests:', error);
      throw error;
    }
  }
}
