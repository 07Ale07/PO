import CA from './CA';

export default async function IS(usuario: string, clave: string): Promise<boolean> {
  try {
    const res = await fetch(`${CA}/usuarios`);
    const datos = await res.json();
    const encontrado = datos.find(
      (u: any) => u.usuario === usuario && u.clave === clave
    );
    return !!encontrado;
  } catch (error) {
    console.error('Error en el login:', error);
    return false;
  }
}
