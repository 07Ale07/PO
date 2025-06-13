// navigation/AdminStack.tsx
import React from 'react';
import { createStackNavigator } from '@react-navigation/stack';
import Admin from '../views/Admin';
import MV from '../views/MV'; // Asegúrate de que este archivo exista y esté exportado correctamente

export type AdminStackParamList = {
  Admin: undefined;
  MV: undefined;
};

const Stack = createStackNavigator<AdminStackParamList>();

export default function AdminStack() {
  return (
    <Stack.Navigator>
      <Stack.Screen name="Admin" component={Admin} options={{ title: 'Panel Admin' }} />
      <Stack.Screen name="MV" component={MV} options={{ title: 'Más Vendido' }} />
    </Stack.Navigator>
  );
}
