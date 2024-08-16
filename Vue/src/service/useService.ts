import apiClient from '@/axios';
import { type Service } from '../types/service';

const API_URL = "http://hotel-manager.test/api/service";

export class useService {
  public async getServices(): Promise<Service[]> {
    try {
      const response = await apiClient.get(API_URL);
      return response.data as Service[];
    } catch (error) {
      console.error('Error fetching services:', error);
      throw error;
    }
  }
}
