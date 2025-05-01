<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Navbar from '@/Components/Navbar.vue'

const logout = () => {
    router.post(route('logout'));
};

const props = defineProps({
    users: Object,
    articulos: Object,
    pedidos: Object,
});

// Referencia para almacenar el artículo que está siendo editado
const editandoArticulo = ref(null);
// Referencia para el valor del descuento
const descuento = ref(0);

// Función para abrir el modal de edición de descuento
const editarDescuento = (articulo) => {
    editandoArticulo.value = articulo;
    descuento.value = articulo.descuento || 0;
};

// Función para guardar el descuento
const guardarDescuento = () => {
    if (editandoArticulo.value) {
        router.post(route('articulos.descuento', { articulo: editandoArticulo.value.id }), {
            descuento: descuento.value,
        }, {
            onSuccess: () => {
                editandoArticulo.value = null;
            },
        });
    }
};

// Función para cancelar la edición
const cancelarEdicion = () => {
    editandoArticulo.value = null;
};

// Función para ir a la página de edición del artículo
const irAEditarArticulo = (articuloId) => {
    router.visit(route('articulos.edit', { articulo: articuloId }));
};

// Función para eliminar un artículo
const eliminarArticulo = (articuloId) => {
    if (confirm('¿Estás seguro de que deseas eliminar este artículo? Esta acción no se puede deshacer.')) {
        router.delete(route('articulos.destroy', { articulo: articuloId }));
    }
};

// Función para limitar la longitud del texto
const limitarTexto = (texto, longitud) => {
    if (texto.length > longitud) {
        return texto.slice(0, longitud) + '...';
    }
    return texto;
};
</script>

<template>
    <Head title="Admin Dashboard" />
    <div class="bg-gray-50 text-gray-800 dark:bg-black dark:text-white min-h-screen">
        <div class="flex flex-col selection:bg-purple-500 selection:text-white dark:selection:bg-[#FF2D20]">
            <Navbar />

            <!-- Título del dashboard con estilo actualizado -->
            <div class="px-8 flex flex-row min-w-full border-b border-gray-300 dark:border-white/20 bg-gradient-to-r from-purple-50 to-transparent dark:from-purple-900/10">
                <h2 class="pt-6 pb-6 font-extrabold text-4xl text-gray-800 dark:text-white flex items-center">
                    Dashboard
                </h2>
            </div>

            <div class="container mx-auto py-8 px-4 w-full max-w-full lg:max-w-[95%]">
                <!-- Listado de usuarios -->
                <div class="bg-white dark:bg-gray-800/40 rounded-xl shadow-sm overflow-hidden border border-gray-200 dark:border-white/5 mb-8">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            Listado de Usuarios
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800/60">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Teléfono</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dirección</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dirección Facturación</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rol</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-transparent divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ user.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ user.telefono }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ user.direccion_envio }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ user.direccion_facturacion }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                              :class="user.rol === 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'">
                                            {{ user.rol }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Listado de artículos -->
                <div class="bg-white dark:bg-gray-800/40 rounded-xl shadow-sm overflow-hidden border border-gray-200 dark:border-white/5 mb-8">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            Listado de Artículos
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-fixed">
                            <thead class="bg-gray-50 dark:bg-gray-800/60">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-[15%]">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-[10%]">Categoría</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-[30%]">Descripción</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-[8%]">Precio</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-[10%]">Descuento</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-[12%]">Última Actualización</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-[15%]">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-transparent divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="articulo in articulos" :key="articulo.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white truncate">{{ articulo.nombre }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 truncate">{{ articulo.categoria }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        <div class="truncate" :title="articulo.descripcion_short">
                                            {{ limitarTexto(articulo.descripcion_short, 100) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ articulo.precio }} €</td>
                                    <td class="px-6 py-4">
                                        <span v-if="articulo.descuento > 0" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            {{ articulo.descuento }}%
                                        </span>
                                        <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            Sin descuento
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ articulo.updated_at.slice(0,10) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        <div class="flex space-x-2">
                                            <button @click="editarDescuento(articulo)"
                                                class="bg-purple-600 hover:bg-purple-700 text-white py-1 px-3 rounded text-xs font-medium transition-colors shadow-sm">
                                                Editar descuento
                                            </button>
                                            <button @click="irAEditarArticulo(articulo.id)"
                                                class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded text-xs font-medium transition-colors shadow-sm">
                                                Editar
                                            </button>
                                            <button @click="eliminarArticulo(articulo.id)"
                                                class="bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded text-xs font-medium transition-colors shadow-sm">
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Listado de pedidos -->
                <div class="bg-white dark:bg-gray-800/40 rounded-xl shadow-sm overflow-hidden border border-gray-200 dark:border-white/5">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                            </svg>
                            Listado de Pedidos
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800/60">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cliente</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dirección</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Última Actualización</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-transparent divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="pedido in pedidos" :key="pedido.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ users.find(user => user.id === pedido.user_id)?.name || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                              :class="{
                                                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300': pedido.estado === 'Completado',
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300': pedido.estado === 'Pendiente',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300': pedido.estado === 'En proceso',
                                                'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300': pedido.estado === 'Cancelado'
                                              }">
                                            {{ pedido.estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ pedido.direccion_envio }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ pedido.updated_at.slice(0,10) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer minimalista -->
            <footer class="bg-white dark:bg-gray-900 py-8 border-t border-gray-200 dark:border-gray-800 mt-12">
                <div class="container mx-auto px-4 text-center">
                    <p class="text-gray-600 dark:text-gray-400">© 2025 Caveman - Todos los derechos reservados</p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal de edición de descuento -->
    <div v-if="editandoArticulo" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-96 relative">
            <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">
                Editar descuento para {{ editandoArticulo.nombre }}
            </h3>
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Descuento (en porcentaje)
                </label>
                <input type="number" v-model="descuento" min="0" max="100"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-purple-500 dark:focus:border-purple-400"
                    placeholder="Ingrese el porcentaje de descuento">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Establezca a 0 para eliminar el descuento.
                </p>
            </div>
            <div class="flex justify-end space-x-2">
                <button @click="cancelarEdicion"
                    class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded transition-colors">
                    Cancelar
                </button>
                <button @click="guardarDescuento"
                    class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded transition-colors">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</template>
