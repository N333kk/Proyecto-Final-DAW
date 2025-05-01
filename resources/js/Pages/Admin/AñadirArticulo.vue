<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

// Vista previa de la imagen
const previewImage = ref('');

const form = useForm({
    nombre: '',
    imagen: null,
    categoria_id: '1',
    descripcion: '',
    descripcion_short: '',
    precio: 0
});

// Función para manejar la subida de imágenes
const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    // Crear URL para la vista previa
    previewImage.value = URL.createObjectURL(file);

    // Asignar archivo al formulario
    form.imagen = file;
};

const submitForm = () => {
    // Enviar formulario con multipart/form-data para manejar archivos
    form.post('/articulos', {
        forceFormData: true,
        onSuccess: () => {
            // Liberar URL de la vista previa
            if (previewImage.value) {
                URL.revokeObjectURL(previewImage.value);
                previewImage.value = '';
            }
        }
    });
};
</script>

<template>
    <div class="min-h-screen p-8 min-w-screen bg-zinc-900 text-white flex items-center flex-col">
        <div class="px-16 py-8 bg-gradient-to-b from-zinc-700 to-zinc-500 border rounded-xl border-slate-400 text-white flex items-center flex-col">

            <h1 class="font-bold text-lg mb-4"> Crear un Articulo </h1>

            <form @submit.prevent="submitForm" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-white text-sm font-bold mb-2" for="nombre">Nombre</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           type="text" v-model="form.nombre" placeholder="Nombre" id="nombre"/>
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
                    </select>
                    <div v-if="form.errors.categoria_id" class="text-red-500 text-xs italic mt-4">
                        {{ form.errors.categoria_id }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-white text-sm font-bold mb-2" for="descripcion">Descripcion</label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                          v-model="form.descripcion" placeholder="Descripcion" id="descripcion" rows="6"></textarea>
                    <div v-if="form.errors.descripcion" class="text-red-500 text-xs italic mt-4">
                        {{ form.errors.descripcion }}
                    </div>
                    <div class="text-gray-300 text-xs mt-2">
                        Puedes usar formatos como **negrita**, *cursiva*, y listas con - o *
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-white text-sm font-bold mb-2" for="descripcion">Descripcion Corta</label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                          v-model="form.descripcion_short" placeholder="Descripcion Corta" id="descripcion_short" rows="6"></textarea>
                    <div v-if="form.errors.descripcion_short" class="text-red-500 text-xs italic mt-4">
                        {{ form.errors.descripcion_short }}
                    </div>
                    <div class="text-gray-300 text-xs mt-2">
                        Esta descripcion se mostrara en el listado de articulos y en el carrito.
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-white text-sm font-bold mb-2" for="imagen">Imagen</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           type="file" @change="handleImageUpload" accept="image/*" id="imagen"/>

                    <!-- Vista previa de la imagen -->
                    <div v-if="previewImage" class="mt-3 w-full">
                        <img :src="previewImage" alt="Vista previa" class="h-48 object-contain mx-auto rounded" />
                    </div>

                    <div v-if="form.errors.imagen" class="text-red-500 text-xs italic mt-4">
                        {{ form.errors.imagen }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-white text-sm font-bold mb-2" for="precio">Precio</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           type="number" v-model="form.precio" placeholder="Precio" id="precio"/>
                    <div v-if="form.errors.precio" class="text-red-500 text-xs italic mt-4">
                        {{ form.errors.precio }}
                    </div>
                </div>

                <button type="submit" :disabled="form.processing"
                        class="bg-zinc-800 hover:bg-zinc-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    {{ form.processing ? 'Procesando...' : 'Añadir Artículo' }}
                </button>
            </form>
        </div>
    </div>
</template>
