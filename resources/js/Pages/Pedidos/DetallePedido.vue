<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';

defineProps({
    pedido: Object,
    categorias: Array
});

// Función para formatear la fecha
const formatearFecha = (fecha) => {
    if (!fecha) return 'N/A';
    return new Date(fecha).toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

// Calcular el precio total del pedido
const calcularTotal = (items) => {
    if (!items || !items.length) return 0;
    return items.reduce((total, item) => {
        return total + (item.precio * item.cantidad);
    }, 0);
};
</script>

<template>
    <Head title="Detalle de Pedido" />
    <div class="bg-gray-50 text-gray-800 dark:bg-black dark:text-white min-h-screen">
        <div class="flex flex-col items-center justify-center selection:bg-purple-500 selection:text-white dark:selection:bg-[#FF2D20]">
            <Navbar :categorias="categorias" />

            <!-- Título del pedido con estilo actualizado -->
            <div class="px-8 flex flex-row min-w-full border-b border-gray-300 dark:border-white/20 bg-gradient-to-r from-purple-50 to-transparent dark:from-purple-900/10">
                <h2 class="pt-6 pb-6 font-extrabold text-4xl text-gray-800 dark:text-white flex items-center">
                    Pedido #{{ pedido.id }}
                    <span :class="{
                        'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300': pedido.estado === 'Completado',
                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300': pedido.estado === 'Pendiente',
                        'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300': pedido.estado === 'En proceso',
                        'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300': pedido.estado === 'Cancelado'
                    }" class="ml-4 px-3 py-1 rounded-full text-sm font-medium">
                        {{ pedido.estado }}
                    </span>
                </h2>
            </div>

            <div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8 w-full">
                <!-- Información general del pedido con estilos actualizados -->
                <div class="flex flex-col md:flex-row gap-6 mb-8">
                    <!-- Información del pedido -->
                    <div class="w-full md:w-1/2">
                        <div class="bg-white dark:bg-gray-800/40 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-white/5 h-full">
                            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                                </svg>
                                Información del Cliente
                            </h3>
                            <div class="space-y-3 mt-6">
                                <div class="flex items-center">
                                    <span class="font-medium text-gray-800 dark:text-white min-w-36">Cliente:</span>
                                    <span class="text-gray-700 dark:text-gray-300">{{ pedido.user.name }}</span>
                                </div>
                                <div class="flex items-start">
                                    <span class="font-medium text-gray-800 dark:text-white min-w-36">Dirección de envio:</span>
                                    <span class="text-gray-700 dark:text-gray-300">{{ pedido.direccion_envio }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del estado y fechas -->
                    <div class="w-full md:w-1/2">
                        <div class="bg-gradient-to-br from-white to-purple-50 dark:from-gray-800/80 dark:to-purple-900/30 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-white/5 h-full">
                            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                Estado y Fechas
                            </h3>
                            <div class="space-y-3 mt-6">
                                <div class="flex items-center">
                                    <span class="font-medium text-gray-800 dark:text-white min-w-32">Fecha de creación:</span>
                                    <span class="text-gray-700 dark:text-gray-300">{{ formatearFecha(pedido.created_at) }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="font-medium text-gray-800 dark:text-white min-w-32">Última actualización:</span>
                                    <span class="text-gray-700 dark:text-gray-300">{{ formatearFecha(pedido.updated_at) }}</span>
                                </div>
                                <div class="flex items-center mt-4 pt-4 border-t border-gray-200 dark:border-white/10">
                                    <span class="font-medium text-gray-800 dark:text-white min-w-32">Total del pedido:</span>
                                    <span class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ calcularTotal(pedido.articulos_pedido) }} €</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Listado de artículos con estilos mejorados -->
                <div class="bg-white dark:bg-gray-800/40 rounded-xl shadow-sm overflow-hidden border border-gray-200 dark:border-white/5">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            Artículos del Pedido
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800/70">
                                <tr>
                                    <th scope="col" class="py-3.5 px-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Artículo</th>
                                    <th scope="col" class="py-3.5 px-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Talla</th>
                                    <th scope="col" class="py-3.5 px-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Precio Unitario</th>
                                    <th scope="col" class="py-3.5 px-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cantidad</th>
                                    <th scope="col" class="py-3.5 px-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-transparent divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="item in pedido.articulos_pedido" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-14 w-14 rounded-md overflow-hidden bg-gray-100 dark:bg-gray-700">
                                                <img v-if="item.articulo.imagenes && item.articulo.imagenes.length > 0"
                                                    :src="`${item.articulo.imagenes[0].ruta}`"
                                                    :alt="item.articulo.nombre"
                                                    class="h-full w-full object-cover">
                                                <div v-else class="h-full w-full flex items-center justify-center text-gray-400">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ item.articulo.nombre }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ item.articulo.descripcion_corta }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-sm text-gray-900 dark:text-gray-200 font-medium">
                                        <div v-if="item.talla" class="px-2 py-1 inline-block bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium rounded-md">
                                            {{ item.talla.talla }}
                                        </div>
                                        <span v-else class="text-gray-500 dark:text-gray-400">No aplica</span>
                                    </td>
                                    <td class="py-4 px-4 text-sm text-gray-900 dark:text-gray-200 font-medium">{{ item.precio }} €</td>
                                    <td class="py-4 px-4 text-sm text-gray-900 dark:text-gray-200">{{ item.cantidad }}</td>
                                    <td class="py-4 px-4 text-sm font-semibold text-purple-600 dark:text-purple-400">{{ (item.precio * item.cantidad).toFixed(2) }} €</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50 dark:bg-gray-800/70">
                                    <td colspan="4" class="py-4 px-4 text-sm font-semibold text-right text-gray-900 dark:text-white">Total:</td>
                                    <td class="py-4 px-4 text-lg font-bold text-purple-700 dark:text-purple-400">{{ calcularTotal(pedido.articulos_pedido).toFixed(2) }} €</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Botón para volver -->
                <div class="mt-8 flex justify-end">
                    <Link href="/pedidos" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-white font-medium rounded-lg transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                        </svg>
                        Volver a mis pedidos
                    </Link>
                </div>
            </div>

            <!-- Footer minimalista -->
            <footer class="bg-white dark:bg-gray-900 py-8 border-t border-gray-200 dark:border-gray-800 w-full mt-12">
                <div class="container mx-auto px-4 text-center">
                    <p class="text-gray-600 dark:text-gray-400">© 2025 Caveman - Todos los derechos reservados</p>
                </div>
            </footer>
        </div>
    </div>
</template>
