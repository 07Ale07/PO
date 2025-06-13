import React, { useState } from 'react';
import { View, Text, TextInput, Button, StyleSheet } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createDrawerNavigator } from '@react-navigation/drawer';
import { createNativeStackNavigator } from '@react-navigation/native-stack';

import Admin from './src/views/Admin';
import MV from './src/views/MV';
import MenuDrawer from './src/views/MenuDrawer';
import IS from './src/logic/IS';

const Drawer = createDrawerNavigator();
const Stack = createNativeStackNavigator();

// 🚀 Stack para vistas de administración
function AdminStack() {
  return (
    <Stack.Navigator initialRouteName="Admin">
      <Stack.Screen name="Admin" component={Admin} options={{ title: 'Panel Admin' }} />
      <Stack.Screen name="MV" component={MV} options={{ title: 'Más Vendido' }} />
      {/* Puedes agregar más pantallas aquí */}
    </Stack.Navigator>
  );
}

export default function App() {
  const [usuario, setUsuario] = useState('');
  const [clave, setClave] = useState('');
  const [logeado, setLogeado] = useState(false);

  const handleLogin = async () => {
    const success = await IS(usuario, clave);
    if (success) {
      setLogeado(true);
    } else {
      alert('Credenciales incorrectas');
    }
  };

  if (!logeado) {
    return (
      <View style={styles.container}>
        <Text style={styles.titulo}>Inicio de sesión</Text>
        <TextInput
          placeholder="Usuario"
          style={styles.input}
          value={usuario}
          onChangeText={setUsuario}
          autoCapitalize="none"
        />
        <TextInput
          placeholder="Clave"
          style={styles.input}
          secureTextEntry
          value={clave}
          onChangeText={setClave}
        />
        <Button title="Ingresar" onPress={handleLogin} />
      </View>
    );
  }

  return (
    <NavigationContainer>
      <Drawer.Navigator
        initialRouteName="AdminStack"
        drawerContent={(props) => <MenuDrawer {...props} />}
      >
        <Drawer.Screen
          name="AdminStack"
          component={AdminStack}
          options={{ title: 'Administración' }}
        />
        {/* Aquí puedes agregar más stacks o vistas independientes si es necesario */}
      </Drawer.Navigator>
    </NavigationContainer>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, justifyContent: 'center', padding: 20 },
  titulo: { fontSize: 20, marginBottom: 20, textAlign: 'center' },
  input: {
    borderWidth: 1,
    borderColor: '#ccc',
    padding: 10,
    marginBottom: 10,
    borderRadius: 6,
  },
});
