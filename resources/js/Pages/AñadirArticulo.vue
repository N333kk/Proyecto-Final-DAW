<script setup>

import { useForm } from '@inertiajs/vue3';

const form = useForm({
    nombre: '',
    imagen: '',
    categoria: '',
    descripcion: '',
    precio: 0
});

</script>

<template>
    <div class="min-h-screen p-8 min-w-screen bg-zinc-900 text-white flex items-center flex-col">

    <h1> Crear un llibre </h1>

    <form @submit.prevent="form.post('/articulos')">
        <div>
            <label for="nombre">Nombre</label>
            <input type="text" v-model="form.nombre" placeholder="Nombre" id="nombre"/>
            <div v-if="form.errors.nombre">
                {{ form.errors.nombre }}
            </div>
        </div>

        <div>
            <label for="categoria">Categoria</label>
            <input type="text" v-model="form.categoria" placeholder="Categoria" id="categoria"/>
            <div v-if="form.errors.categoria">
                {{ form.errors.categoria }}
            </div>
        </div>

        <div>
            <label for="descripcion">Descripcion</label>
            <input type="text" v-model="form.descripcion" placeholder="Descripcion" id="descripcion"/>
            <div v-if="form.errors.descripcion">
                {{ form.errors.descripcion }}
            </div>
        </div>

        <div>
            <label for="imagen">Imagen</label>
            <input type="text" v-model="form.imagen" placeholder="Imagen" id="imagen"/>
            <div v-if="form.errors.imagen">
                {{ form.errors.imagen }}
            </div>
        </div>

        <div>
            <label for="precio">Precio</label>
            <input type="number" v-model="form.precio" placeholder="Precio" id="precio"/>
            <div v-if="form.errors.precio">
                {{ form.errors.precio }}
            </div>
        </div>

        <div>

        </div>
        <button type="submit" :disabled="form.processing">Añadir Articulo.</button>
    </form>

</div>

</template>
