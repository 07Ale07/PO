export const api = {
    baseUrl: 'http://10.0.13.99:3000',
  
    async post(endpoint: string, data: any) {
      return fetch(`${this.baseUrl}${endpoint}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
      });
    },
  
    async get(endpoint: string) {
      return fetch(`${this.baseUrl}${endpoint}`);
    },
  };
  