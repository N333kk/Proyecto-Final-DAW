<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';

const logout = () => {
    router.post(route('logout'));
};

defineProps({
    pedidos: Array,
    usuarios: Array,
    categorias: Array
});

</script>

<template>
<Head title="Pedidos" />
    <div class="bg-gray-50 text-gray-800 dark:bg-black dark:text-white min-h-screen">
        <div class="flex flex-col items-center justify-center selection:bg-purple-500 selection:text-white dark:selection:bg-[#FF2D20]">
            <Navbar :categorias="categorias" />

            <div class="px-8 flex flex-row min-w-full border-b border-gray-300 dark:border-white/20 bg-gradient-to-r from-purple-50 to-transparent dark:from-purple-900/10">
                <h2 class="pt-6 pb-6 font-extrabold text-4xl text-gray-800 dark:text-white flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 h-10 mr-3 text-purple-600">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                    </svg>
                    Mis Pedidos
                </h2>
            </div>

            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 w-full">
                <div class="bg-timberwolf/40 dark:bg-slate-800/40 rounded-xl p-6 border border-linen/30 shadow-lg mb-8">
                    <h2 class="text-2xl font-semibold text-black dark:text-white mb-6 text-center">Listado de pedidos</h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-slate-900/50 rounded-lg overflow-hidden">
                            <thead class="bg-purple-700/55 dark:bg-purple-900/40 text-white">
                                <tr>
                                    <th class="py-3 px-4 text-left font-semibold">ID</th>
                                    <th class="py-3 px-4 text-left font-semibold">Estado</th>
                                    <th class="py-3 px-4 text-left font-semibold">Dirección de envío</th>
                                    <th class="py-3 px-4 text-left font-semibold">Usuario</th>
                                    <th class="py-3 px-4 text-left font-semibold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="pedido in pedidos" :key="pedido.id"
                                    class="border-b border-gray-200 dark:border-gray-700 hover:bg-isabelline/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="py-3 px-4 text-black dark:text-white">{{ pedido.id }}</td>
                                    <td class="py-3 px-4">
                                        <span :class="{
                                            'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300': pedido.estado === 'Completado',
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300': pedido.estado === 'Pendiente',
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300': pedido.estado === 'En proceso',
                                            'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300': pedido.estado === 'Cancelado'
                                        }" class="px-2 py-1 rounded-full text-xs font-semibold">
                                            {{ pedido.estado }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-black dark:text-white">{{ pedido.direccion_envio }}</td>
                                    <td class="py-3 px-4 text-black dark:text-white">{{ pedido.user.name }}</td>
                                    <td class="py-3 px-4 flex space-x-2">
                                        <Link :href="`/pedidos/${pedido.id}/detalle`"
                                            class="bg-purple-500/90 hover:bg-purple-600 text-white font-medium py-1 px-3 rounded-md transition-colors text-sm inline-flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Ver detalles
                                        </Link>
                                        <Link v-if="$page.props.auth.user.rol === 'admin'" :href="`/pedidos/${pedido.id}/edit`"
                                            class="bg-blue-500/90 hover:bg-blue-600 text-white font-medium py-1 px-3 rounded-md transition-colors text-sm inline-flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21h-9.5A2.25 2.25 0 014 18.75V8.25A2.25 2.25 0 016.25 6H11" />
                                            </svg>
                                            Editar
                                        </Link>
                                        <Link
                                            v-if="$page.props.auth.user.rol === 'admin' || $page.props.auth.user.id === pedido.user_id && pedido.estado === 'Pendiente'"
                                            :href="`/pedidos/${pedido.id}`" method="delete"
                                            class="bg-red-500/90 hover:bg-red-600 text-white font-medium py-1 px-3 rounded-md transition-colors text-sm inline-flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                            Cancelar
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
