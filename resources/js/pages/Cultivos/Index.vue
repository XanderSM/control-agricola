<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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

defineProps<{
    cultivos: Cultivo[];
    parcelas: Parcela[];
}>();

const isEditing = ref(false);

const form = useForm({
    id: null as number | null,
    parcela_id: 0 as number,
    producto: '',
    fecha_siembra: '',
    imagen: null as File | null,
});

const submit = () => {
    if (isEditing.value && form.id) {
        form.put(`/cultivos/${form.id}`, {
            forceFormData: true,
            onSuccess: () => cancelEdit(),
            onError: () => {
                // Los errores se muestran en el template con form.errors
                console.error('Error al actualizar cultivo');
            },
        });
    } else {
        form.post('/cultivos', {
            forceFormData: true,
            onSuccess: () => {
                form.reset();
                isEditing.value = false;
            },
            onError: () => {
                console.error('Error al crear cultivo');
            },
        });
    }
};

const edit = (cultivo: Cultivo) => {
    isEditing.value = true;
    form.id = cultivo.id;
    form.parcela_id = cultivo.parcela_id;
    form.producto = cultivo.producto;
    form.fecha_siembra = cultivo.fecha_siembra;
    form.imagen = null;
};

const cancelEdit = () => {
    isEditing.value = false;
    form.reset();
};

const handleImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.imagen = target.files?.[0] || null;
};

const destroy = (id: number) => {
    if (confirm("¿Estás seguro de eliminar este registro?")) {
        router.delete(`/cultivos/${id}`, {
            onError: () => {
                alert('Error al eliminar el registro');
            },
        });
    }
};
</script>

<template>
    <div class="p-6 max-w-7xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Control Agrícola - Examen</h1>

        <form @submit.prevent="submit" class="bg-white p-6 rounded shadow mb-8">
            <h2 class="font-bold mb-4">{{ isEditing ? 'Editar Cultivo' : 'Nuevo Cultivo' }}</h2>
            <div class="grid grid-cols-1 gap-4">
                <!-- Parcela Select -->
                <div>
                    <select v-model.number="form.parcela_id" required class="w-full border p-2 rounded" :class="{ 'border-red-500': form.errors.parcela_id }">
                        <option :value="0" disabled>Seleccione una Parcela</option>
                        <option v-for="parcela in parcelas" :key="parcela.id" :value="parcela.id">
                            {{ parcela.nombre }}
                        </option>
                    </select>
                    <p v-if="form.errors.parcela_id" class="text-red-500 text-sm mt-1">{{ form.errors.parcela_id }}</p>
                </div>

                <!-- Producto Input -->
                <div>
                    <input v-model="form.producto" type="text" placeholder="Producto (Ej. Maíz)" required class="w-full border p-2 rounded" :class="{ 'border-red-500': form.errors.producto }">
                    <p v-if="form.errors.producto" class="text-red-500 text-sm mt-1">{{ form.errors.producto }}</p>
                </div>

                <!-- Fecha Siembra Input -->
                <div>
                    <input v-model="form.fecha_siembra" type="date" required class="w-full border p-2 rounded" :class="{ 'border-red-500': form.errors.fecha_siembra }">
                    <p v-if="form.errors.fecha_siembra" class="text-red-500 text-sm mt-1">{{ form.errors.fecha_siembra }}</p>
                </div>

                <!-- Imagen Input -->
                <div>
                    <input type="file" @change="handleImageChange" accept="image/*" :required="!isEditing" class="w-full border p-2 rounded" :class="{ 'border-red-500': form.errors.imagen }">
                    <p v-if="!isEditing" class="text-gray-600 text-sm mt-1">Requerido para crear</p>
                    <p v-if="isEditing" class="text-gray-600 text-sm mt-1">Opcional: solo para cambiar imagen</p>
                    <p v-if="form.errors.imagen" class="text-red-500 text-sm mt-1">{{ form.errors.imagen }}</p>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-500 text-white p-2 rounded font-bold w-full hover:bg-blue-600 disabled:opacity-50" :disabled="form.processing">
                        {{ form.processing ? 'Procesando...' : (isEditing ? 'Actualizar' : 'Guardar') }}
                    </button>
                    <button v-if="isEditing" type="button" @click="cancelEdit" class="bg-gray-500 text-white p-2 rounded font-bold w-full hover:bg-gray-600">
                        Cancelar
                    </button>
                </div>
            </div>
        </form>

        <table class="w-full bg-white shadow rounded border-collapse overflow-hidden">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Parcela</th>
                    <th class="border p-2">Producto</th>
                    <th class="border p-2">Fecha</th>
                    <th class="border p-2">Imagen</th>
                    <th class="border p-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="cultivos.length === 0" class="text-center">
                    <td colspan="6" class="border p-4 text-gray-500">No hay cultivos registrados</td>
                </tr>
                <tr v-for="cultivo in cultivos" :key="cultivo.id" class="text-center hover:bg-gray-50">
                    <td class="border p-2">{{ cultivo.id }}</td>
                    <td class="border p-2">{{ cultivo.parcela?.nombre ?? 'N/A' }}</td>
                    <td class="border p-2">{{ cultivo.producto }}</td>
                    <td class="border p-2">{{ cultivo.fecha_siembra }}</td>
                    <td class="border p-2">
                        <img v-if="cultivo.imagen" :src="`/storage/${cultivo.imagen}`" :alt="`Cultivo ${cultivo.id}`" width="80" class="mx-auto rounded">
                        <span v-else class="text-gray-400">Sin imagen</span>
                    </td>
                    <td class="border p-2 space-x-2">
                        <button @click="edit(cultivo)" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition">Editar</button>
                        <button @click="destroy(cultivo.id)" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>