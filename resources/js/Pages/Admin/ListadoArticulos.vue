<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    articulos: Object,
});

</script>

<template>

    <div class="min-h-screen p-8 min-w-screen bg-zinc-900 text-white flex items-center flex-col">
        <h2 class="p-4 border border-slate-700 rounded-xl bg-slate-800">Listado de articulos</h2>
    <ul class="p-8 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">

        <li class="bg-white p-6 rounded-lg shadow-md flex text-black" v-for="articulo in articulos" :key="articulo.id">
            <div class="flex-shrink-0 p-1">
                <img class="w-24 h-36 object-cover rounded" :src="`${articulo.imagen}`" alt="Imagen Articulo">
            </div>
            <div class="ml-4">
            <h2 class="text-lg font-semibold mb-2">{{ articulo.nombre }}</h2>
            <p class="text-blue-700 font-bold">{{ articulo.categoria }}</p>
            <p class="text-blue-700">{{ articulo.descripcion }}</p>
            <p class="text-blue-700 font-semibold">{{ articulo.precio }} €</p>

            <Link :href="`/articulos/${articulo.id}/edit`" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2 mx-1 inline-block">Editar</Link>
            <Link :href="`/articulos/${articulo.id}`" method="DELETE" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mt-2  mx-1 inline-block">Eliminar</Link>
            </div>
        </li>
    </ul>
    </div>

</template>
