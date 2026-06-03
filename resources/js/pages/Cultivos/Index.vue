<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

// 1. Definimos las "interfaces" para decirle a TypeScript qué estructura tienen tus datos
interface Parcela {
    id: number;
    nombre: string;
    ubicacion: string;
}

interface Cultivo {
    id: number;
    parcela_id: number;
    producto: string;
    fecha_siembra: string;
    imagen: string | null;
    parcela?: Parcela;
}

// 2. Declaramos los Props usando las interfaces
defineProps<{
    cultivos: Cultivo[];
    parcelas: Parcela[];
}>();

// 3. Inicializamos el formulario especificando los tipos
const form = useForm({
    parcela_id: '' as string | number,
    producto: '',
    fecha_siembra: '',
    imagen: null as File | null,
});

const submit = () => {
    // Prevención estricta por frontend para evitar procesamiento de números negativos
    if (Number(form.parcela_id) < 0) {
        alert("Error: No se permite procesar números negativos.");

        return;
    }

    // Usamos la URI directa '/cultivos' para evitar el error de TypeScript con el helper route()
    form.post('/cultivos', {
        forceFormData: true,
        onSuccess: () => form.reset(),
    });
};

const handleImagenChange = (event: Event) => {
    const target = event.target as HTMLInputElement | null;

    if (!target?.files?.[0]) {
        return;
    }

    form.imagen = target.files[0];
};
</script>

<template>
    <div class="p-6 max-w-7xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Control Agrícola - Examen</h1>

        <!-- Formulario -->
        <form @submit.prevent="submit" class="bg-white p-6 rounded shadow mb-8">
            <div class="grid grid-cols-1 gap-4">
                <select v-model="form.parcela_id" required class="border p-2 rounded">
                    <option value="" disabled>Seleccione una Parcela</option>
                    <option v-for="parcela in parcelas" :key="parcela.id" :value="parcela.id">
                        {{ parcela.nombre }}
                    </option>
                </select>

                <input v-model="form.producto" type="text" placeholder="Producto (Ej. Maíz)" required class="border p-2 rounded">
                <input v-model="form.fecha_siembra" type="date" required class="border p-2 rounded">
                <input type="file" @change="handleImagenChange" accept="image/*" required class="border p-2 rounded">

                <button type="submit" class="bg-blue-500 text-white p-2 rounded font-bold">Guardar Cultivo</button>
            </div>
        </form>

        <!-- Tabla CRUD -->
        <table class="w-full bg-white shadow rounded border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Parcela</th>
                    <th class="border p-2">Producto</th>
                    <th class="border p-2">Fecha de Siembra</th>
                    <th class="border p-2">Imagen</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="cultivo in cultivos" :key="cultivo.id" class="text-center">
                    <td class="border p-2">{{ cultivo.id }}</td>
                    <td class="border p-2">{{ cultivo.parcela ? cultivo.parcela.nombre : 'N/A' }}</td>
                    <td class="border p-2">{{ cultivo.producto }}</td>
                    <td class="border p-2">{{ cultivo.fecha_siembra }}</td>
                    <td class="border p-2">
                        <img v-if="cultivo.imagen" :src="`/storage/${cultivo.imagen}`" width="80" class="mx-auto rounded" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>