import React from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity } from 'react-native';
import { useNavigation, NavigationProp } from '@react-navigation/native';

// Define los tipos de rutas del Stack AdminStack
type AdminStackParamList = {
  Admin: undefined;
  MV: undefined;
};

const opciones = ['Más Vendido', 'Paquetes Populares', 'Estadísticas'];

export default function Admin() {
  // Obtén el navigation del stack AdminStack con tipo
  const navigation = useNavigation<NavigationProp<AdminStackParamList>>();

  const handlePress = (item: string) => {
    if (item === 'Más Vendido') {
      navigation.navigate('MV');
    }
    // Más opciones aquí...
  };

  return (
    <View style={styles.container}>
      <Text style={styles.titulo}>Panel de Administración</Text>
      <FlatList
        data={opciones}
        keyExtractor={(item) => item}
        renderItem={({ item }) => (
          <TouchableOpacity onPress={() => handlePress(item)} style={styles.opcionContenedor}>
            <Text style={styles.opcion}>• {item}</Text>
          </TouchableOpacity>
        )}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 20 },
  titulo: { fontSize: 24, marginBottom: 10 },
  opcionContenedor: {
    paddingVertical: 10,
    paddingHorizontal: 15,
    backgroundColor: '#eee',
    borderRadius: 6,
    marginBottom: 8,
  },
  opcion: { fontSize: 18 },
});
