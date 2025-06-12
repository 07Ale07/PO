import React from 'react';
import { View, Text, StyleSheet, FlatList } from 'react-native';

const opciones = ['Más Vendido', 'Paquetes Populares', 'Estadísticas'];

export default function Admin() {
  return (
    <View style={styles.container}>
      <Text style={styles.titulo}>Panel de Administración</Text>
      <FlatList
        data={opciones}
        keyExtractor={(item) => item}
        renderItem={({ item }) => (
          <Text style={styles.opcion}>• {item}</Text>
        )}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 20 },
  titulo: { fontSize: 24, marginBottom: 10 },
  opcion: { fontSize: 18, marginBottom: 5 },
});
