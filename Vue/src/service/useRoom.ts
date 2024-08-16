import apiClient from '@/axios';
import { type Room } from '../types/room';

const API_URL = "http://hotel-manager.test/api/room";

export class useRoom {
  public async getRooms(): Promise<Room[]> {
    try {
      const response = await apiClient.get(API_URL);
      return response.data as Room[];
    } catch (error) {
      console.error('Error fetching rooms:', error);
      throw error;
    }
  }
}
