import { api } from '../api/CA';

export default async function IS(usuario: string, clave: string): Promise<boolean> {
  try {
    const response = await api.post('/login', { usuario, clave });

    if (response.ok) {
      const data = await response.json();
      console.log('Login correcto:', data);
      return true;
    } else {
      console.warn('Login fallido. Código:', response.status);
      return false;
    }
  } catch (error) {
    console.error('Error al intentar iniciar sesión:', error);
    return false;
  }
}
