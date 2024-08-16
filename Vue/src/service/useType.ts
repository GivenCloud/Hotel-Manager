import apiClient from '@/axios';
import { type Type } from '../types/type';

const API_URL = "http://hotel-manager.test/api/type";

export class useType {
  public async getTypes(): Promise<Type[]> {
    try {
      const response = await apiClient.get(API_URL);
      return response.data as Type[];
    } catch (error) {
      console.error('Error fetching types:', error);
      throw error;
    }
  }
}
