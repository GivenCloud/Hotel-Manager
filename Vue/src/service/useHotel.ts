import apiClient from '@/axios';
import { type Hotel } from '../types/hotel';
import { type Service } from '@/types/service';

const API_URL = "http://hotel-manager.test/api/hotel";

export class useHotel {
  public async getHotels(): Promise<Hotel[]> {
    try {
      const response = await apiClient.get(API_URL);
      return response.data as Hotel[];
    } catch (error) {
      console.error('Error fetching hotels:', error);
      throw error;
    }
  }

  public async getHotelById(id: number): Promise<Hotel> {
    try {
      const response = await apiClient.get(`${API_URL}/${id}`);
      return response.data as Hotel;
    } catch (error) {
      console.error('Error fetching hotel:', error);
      throw error;
    }
  }

  public async getServicesByHotelId(id: number): Promise<Service[]> {
    try {
      const response = await apiClient.get(`${API_URL}/${id}/services`);
      return response.data as Service[];
    } catch (error) {
      console.error('Error fetching services:', error);
      throw error;
    }
  }
}
