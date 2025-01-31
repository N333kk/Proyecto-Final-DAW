<script setup>

import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    articulo: Object,
});

const form = useForm({
    nombre: props.articulo.nombre,
    categoria: props.articulo.categoria,
    descripcion: props.articulo.descripcion,
    precio: props.articulo.precio,
    imagen: props.articulo.imagen,
});

</script>

<template>

<div class="min-h-screen p-8 min-w-screen bg-zinc-900 text-white flex items-center flex-col">
        <div class="px-16 py-8 bg-gradient-to-b from-zinc-700 to-zinc-500 border rounded-xl border-slate-400 text-white flex items-center flex-col">

            <h1 class="font-bold text-lg mb-4"> Editar Articulo </h1>

            <form @submit.prevent="form.put(`/articulos/${props.articulo.id}`)">
                <div class="mb-4">
                <label class="block text-white text-sm font-bold mb-2" for="nombre">Nombre</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" v-model="form.nombre" placeholder="Nombre" id="nombre"/>
                <div v-if="form.errors.nombre" class="text-red-500 text-xs italic mt-4">
                 {{ form.errors.nombre }}
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-white text-sm font-bold mb-2" for="categoria">Categoria</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" v-model="form.categoria" placeholder="Categoria" id="categoria"/>
                <div v-if="form.errors.categoria" class="text-red-500 text-xs italic mt-4">
                    {{ form.errors.categoria }}
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-white text-sm font-bold mb-2" for="descripcion">Descripcion</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" v-model="form.descripcion" placeholder="Descripcion" id="descripcion"/>
                <div v-if="form.errors.descripcion" class="text-red-500 text-xs italic mt-4">
                    {{ form.errors.descripcion }}
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-white text-sm font-bold mb-2" for="imagen">Imagen</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" v-model="form.imagen" placeholder="Imagen" id="imagen"/>
                <div v-if="form.errors.imagen" class="text-red-500 text-xs italic mt-4">
                    {{ form.errors.imagen }}
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-white text-sm font-bold mb-2" for="precio">Precio</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="number" v-model="form.precio" placeholder="Precio" id="precio"/>
                <div v-if="form.errors.precio" class="text-red-500 text-xs italic mt-4">
                    {{ form.errors.precio }}
                </div>
            </div>

            <div>

            </div>
            <button type="submit" :disabled="form.processing" class="bg-zinc-800 hover:bg-zinc-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Editar Articulo</button>
        </form>
    </div>

</div>
</template>
