<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    articulo: Object,
});

const imagenActual = props.articulo.imagenes && props.articulo.imagenes.length > 0
    ? props.articulo.imagenes[0].ruta
    : null;

const form = useForm({
    nombre: props.articulo.nombre,
    categoria_id: props.articulo.categoria_id || '1',
    descripcion: props.articulo.descripcion,
    precio: props.articulo.precio,
    imagen: null,
    _method: 'put',
});

const previewImage = ref(imagenActual ? `/storage/${imagenActual}` : null);
const nuevaImagen = ref(null);

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    nuevaImagen.value = file;
    form.imagen = file;
    previewImage.value = URL.createObjectURL(file);
};

const submitForm = () => {o
    if (nuevaImagen.value) {
        form.imagen = nuevaImagen.value;
    } else if (imagenActual) {
        form.imagen = imagenActual;
    } else {
        form.imagen = 'error';
    }

    form.post(`/articulos/${props.articulo.id}`, {
        // Solo usamos forceFormData si hay una nueva imagen
        forceFormData: !!nuevaImagen.value,
        onSuccess: () => {
            if (previewImage.value && previewImage.value.startsWith('blob:')) {
                URL.revokeObjectURL(previewImage.value);
            }
        }
    });
};
</script>

<template>

<div class="min-h-screen p-8 min-w-screen bg-zinc-900 text-white flex items-center flex-col">
        <div class="px-16 py-8 bg-gradient-to-b from-zinc-700 to-zinc-500 border rounded-xl border-slate-400 text-white flex items-center flex-col">

            <h1 class="font-bold text-lg mb-4"> Editar Articulo </h1>

            <form @submit.prevent="submitForm" enctype="multipart/form-data">
                <div class="mb-4">
                <label class="block text-white text-sm font-bold mb-2" for="nombre">Nombre</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" v-model="form.nombre" placeholder="Nombre" id="nombre"/>
                <div v-if="form.errors.nombre" class="text-red-500 text-xs italic mt-4">
                 {{ form.errors.nombre }}
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-white text-sm font-bold mb-2" for="categoria_id">Categoría</label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    v-model="form.categoria_id" id="categoria_id" required>
                    <option value="1">Ropa</option>
                    <option value="2">Pantalones</option>
                    <option value="3">Sudaderas</option>
                    <option value="4">Camisetas</option>
                    <option value="5">Skateboards</option>
                    <option value="6">Tablas</option>
                    <option value="7">Ejes</option>
                    <option value="8">Ruedas</option>
                    <!-- Falta añadir categorias, pero hay que cambiar esto por botones seleccionables generados dinamicamente. -->
                </select>
                <div v-if="form.errors.categoria_id" class="text-red-500 text-xs italic mt-4">
                    {{ form.errors.categoria_id }}
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
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    type="file" @change="handleImageChange" accept="image/*" @input="form.imagen" id="imagen"/>

                <!-- Vista previa de la imagen actual o nueva -->
                <div v-if="previewImage" class="mt-2">
                    <img :src="previewImage" alt="Vista previa" class="max-h-40 rounded">
                </div>

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
            <button type="submit" :disabled="form.processing" class="bg-zinc-800 hover:bg-zinc-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                {{ form.processing ? 'Procesando...' : 'Editar Articulo' }}
            </button>
        </form>
    </div>

</div>
</template>
