import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ScrollView, ActivityIndicator } from 'react-native';
import { fetchMasVendido, Estadistica } from '../logic/estadistics';

type Agrupado = {
  [key: string]: { valor: string; cantidad: number }[];
};

const colores = [
  '#FF6384',
  '#36A2EB',
  '#FFCE56',
  '#4BC0C0',
  '#9966FF',
  '#FF9F40',
  '#C9CBCF',
];

const MV: React.FC = () => {
  const [datos, setDatos] = useState<Estadistica[]>([]);
  const [cargando, setCargando] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetchMasVendido()
      .then(setDatos)
      .catch((e) => setError(e.message))
      .finally(() => setCargando(false));
  }, []);

  const agrupados: Agrupado = datos.reduce((acc, curr) => {
    if (!acc[curr.tipo]) acc[curr.tipo] = [];
    acc[curr.tipo].push({ valor: curr.valor, cantidad: curr.cantidad });
    return acc;
  }, {} as Agrupado);

  if (cargando) {
    return (
      <View style={styles.cargaContainer}>
        <ActivityIndicator size="large" />
        <Text>Cargando estadísticas...</Text>
      </View>
    );
  }

  if (error) {
    return (
      <View style={styles.errorContainer}>
        <Text style={styles.errorTexto}>Error: {error}</Text>
      </View>
    );
  }

  return (
    <ScrollView style={styles.container}>
      <Text style={styles.titulo}>Estadísticas de Más Vendido</Text>

      {Object.entries(agrupados).map(([tipo, items]) => (
        <View key={tipo} style={styles.graficoContainer}>
          <Text style={styles.subtitulo}>{tipo}</Text>
          {items.map(({ valor, cantidad }, i) => (
            <View
              key={`${tipo}-${i}`}
              style={[
                styles.itemContainer,
                { backgroundColor: colores[i % colores.length] },
              ]}
            >
              <Text style={styles.itemTexto}>
                {valor}: {cantidad}
              </Text>
            </View>
          ))}
        </View>
      ))}
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, padding: 20, backgroundColor: '#fff' },
  titulo: { fontSize: 24, marginBottom: 15, fontWeight: 'bold' },
  subtitulo: { fontSize: 20, marginBottom: 10, fontWeight: '600' },
  graficoContainer: { marginBottom: 30 },
  itemContainer: {
    marginVertical: 4,
    padding: 8,
    borderRadius: 6,
  },
  itemTexto: { color: '#fff', fontWeight: '600' },
  cargaContainer: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  errorContainer: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  errorTexto: { color: 'red', fontSize: 16 },
});

export default MV;
