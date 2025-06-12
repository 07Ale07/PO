import React, { useState } from 'react';
import { View, Text, TextInput, Button, StyleSheet } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createDrawerNavigator } from '@react-navigation/drawer';
import Admin from './src/views/Admin';
import MenuDrawer from './src/views/MenuDrawer';
import IS from './IS';

const Drawer = createDrawerNavigator();

export default function App() {
  const [usuario, setUsuario] = useState('');
  const [clave, setClave] = useState('');
  const [logeado, setLogeado] = useState(false);

  const handleLogin = async () => {
    const success = await IS(usuario, clave);
    if (success) setLogeado(true);
    else alert('Credenciales incorrectas');
  };

  if (!logeado) {
    return (
      <View style={styles.container}>
        <Text style={styles.titulo}>Inicio de sesión</Text>
        <TextInput placeholder="Usuario" style={styles.input} onChangeText={setUsuario} />
        <TextInput placeholder="Clave" style={styles.input} secureTextEntry onChangeText={setClave} />
        <Button title="Ingresar" onPress={handleLogin} />
      </View>
    );
  }

  return (
    <NavigationContainer>
      <Drawer.Navigator drawerContent={props => <MenuDrawer {...props} />}>
        <Drawer.Screen name="Admin" component={Admin} />
      </Drawer.Navigator>
    </NavigationContainer>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, justifyContent: 'center', padding: 20 },
  titulo: { fontSize: 20, marginBottom: 20, textAlign: 'center' },
  input: { borderWidth: 1, padding: 10, marginBottom: 10 },
});
