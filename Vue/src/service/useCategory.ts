import apiClient from '@/axios';
import { type Category } from '../types/category';

const API_URL = "http://hotel-manager.test/api/category";

export class useCategory {
  public async getCategories(): Promise<Category[]> {
    try {
      const response = await apiClient.get(API_URL);
      return response.data as Category[];
    } catch (error) {
      console.error('Error fetching categories:', error);
      throw error;
    }
  }
}
