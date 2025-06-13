import { api } from '../api/CA';

export type Estadistica = {
  tipo: 'Destino' | 'Hotel' | 'Vehículo';
  valor: string;
  cantidad: number;
};

export async function fetchMasVendido(): Promise<Estadistica[]> {
  try {
    const response = await api.get('/mas_vendido');
    console.log('fetchMasVendido response.data:', response.data);

    // Validar que response.data sea un arreglo
    const data: Estadistica[] = Array.isArray(response.data) ? response.data : [];

    // Ordenar si hay datos
    if (data.length > 0) {
      data.sort((a, b) => {
        if (a.tipo < b.tipo) return -1;
        if (a.tipo > b.tipo) return 1;
        return b.cantidad - a.cantidad;
      });
    }

    return data;
  } catch (error: any) {
    console.error('Error fetchMasVendido:', error.message || error);
    throw error;
  }
}
