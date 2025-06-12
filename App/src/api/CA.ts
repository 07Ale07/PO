export const api = {
  baseUrl: 'http://192.168.0.19:3000', // Asegúrate de usar tu IP local correcta

  async post(endpoint: string, data: any) {
    return fetch(`${this.baseUrl}${endpoint}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    });
  },

  async get(endpoint: string) {
    const response = await fetch(`${this.baseUrl}${endpoint}`);
    return response.json();
  },
};
