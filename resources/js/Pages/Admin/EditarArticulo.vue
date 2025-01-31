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
        <h2 class="p-4 border">Edicion</h2>
        <div>
            <form @submit.prevent="form.put(`/articulos/${props.articulo.id}`);">
                <div>
                    <label for="nombre">Nombre</label>
                    <input type="text" v-model="form.nombre" id="nombre" name="nombre">
                    <div v-if="form.errors.nombre" class="text-red-500">
                        {{ form.errors.nombre }}
                    </div>

                </div>

                <div>
                    <label for="categoria">Categoria</label>
                    <input type="text" v-model="form.categoria" id="categoria" name="categoria">
                    <div v-if="form.errors.categoria" class="text-red-500">
                        {{ form.errors.categoria }}
                    </div>

                </div>

                <div>
                    <label for="descripcion">Descripcion</label>
                    <input type="text" v-model="form.descripcion" id="descripcion" name="descripcion">
                    <div v-if="form.errors.descripcion" class="text-red-500">
                        {{ form.errors.descripcion }}
                    </div>

                </div>

                <div>
                    <label for="precio">Precio</label>
                    <input type="numeric" v-model="form.precio" id="precio" name="precio">
                    <div v-if="form.errors.precio" class="text-red-500">
                        {{ form.errors.precio }}
                    </div>

                </div>

                <div>
                    <label for="imagen">Imagen</label>
                    <input type="string" v-model="form.imagen" id="imagen" name="imagen">
                    <div v-if="form.errors.imagen" class="text-red-500">
                        {{ form.errors.imagen }}
                    </div>

                </div>

                <button type="submit" :disabled="form.processing">
                        Actualizar articulo
                    </button>
            </form>
        </div>
        </div>
</template>
