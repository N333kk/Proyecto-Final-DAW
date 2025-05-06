<script setup>
import { Link, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    tallas: Array,
});

const message = ref(null);
const error = ref(null);

// Si hay un mensaje o error en la sesión, mostrarlo
if (props.flash && props.flash.message) {
    message.value = props.flash.message;
    setTimeout(() => {
        message.value = null;
    }, 5000);
}

if (props.flash && props.flash.error) {
    error.value = props.flash.error;
    setTimeout(() => {
        error.value = null;
    }, 5000);
}
</script>

<template>
    <Head title="Tallas" />
    <div class="min-h-screen bg-zinc-900 text-white p-8">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Gestión de Tallas</h1>
                <Link :href="route('tallas.create')"
                      class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors">
                    Crear Nueva Talla
                </Link>
            </div>

            <!-- Mensajes de alerta -->
            <div v-if="message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
                <span>{{ message }}</span>
                <button @click="message = null" class="text-green-700">
                    <span>&times;</span>
                </button>
            </div>

            <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
                <span>{{ error }}</span>
                <button @click="error = null" class="text-red-700">
                    <span>&times;</span>
                </button>
            </div>

            <div class="bg-zinc-800 rounded-lg shadow-lg overflow-hidden">
                <table class="min-w-full divide-y divide-zinc-700">
                    <thead class="bg-zinc-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Talla</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-700">
                        <tr v-for="talla in tallas" :key="talla.id" class="hover:bg-zinc-750">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">{{ talla.id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">{{ talla.talla }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">
                                <div class="flex space-x-2">
                                    <Link :href="route('tallas.edit', talla.id)"
                                          class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors">
                                        Editar
                                    </Link>
                                    <Link :href="route('tallas.destroy', talla.id)"
                                          method="delete"
                                          as="button"
                                          type="button"
                                          class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded transition-colors"
                                          @click="confirm('¿Está seguro de eliminar esta talla?')">
                                        Eliminar
                                    </Link>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="tallas.length === 0">
                            <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-center text-zinc-300">
                                No hay tallas registradas.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                <Link :href="route('dashboard')"
                      class="px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-white rounded-md transition-colors">
                    Volver al Dashboard
                </Link>
            </div>
        </div>
    </div>
</template>
