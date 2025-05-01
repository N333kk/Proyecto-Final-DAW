<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    articulo: Object,
});

// Almacenamos todas las imágenes actuales
const imagenesActuales = ref(props.articulo.imagenes || []);

// Almacenamos las nuevas imágenes que se van a subir
const nuevasImagenes = ref([]);

// Almacenamos los IDs de las imágenes a eliminar
const imagenesAEliminar = ref([]);

const form = useForm({
    nombre: props.articulo.nombre,
    categoria_id: props.articulo.categoria_id || '1',
    descripcion: props.articulo.descripcion,
    descripcion_short: props.articulo.descripcion_short,
    precio: props.articulo.precio,
    nuevas_imagenes: [],
    imagenes_a_eliminar: [],
    _method: 'put',
});

// Función para manejar la subida de nuevas imágenes
const handleImageUpload = (e) => {
    const files = Array.from(e.target.files);
    if (!files.length) return;

    // Crear previews para las nuevas imágenes y agregarlas al array
    files.forEach(file => {
        const previewUrl = URL.createObjectURL(file);
        nuevasImagenes.value.push({
            file: file,
            preview: previewUrl
        });
    });

    // Actualizar el formulario con las nuevas imágenes
    form.nuevas_imagenes = nuevasImagenes.value.map(img => img.file);
};

// Función para eliminar una imagen existente
const marcarImagenParaEliminar = (imagen) => {
    imagenesAEliminar.value.push(imagen.id);
    imagenesActuales.value = imagenesActuales.value.filter(img => img.id !== imagen.id);
    form.imagenes_a_eliminar = imagenesAEliminar.value;
};

// Función para quitar una imagen nueva antes de subir
const quitarNuevaImagen = (index) => {
    // Liberar el objeto URL para evitar fugas de memoria
    URL.revokeObjectURL(nuevasImagenes.value[index].preview);
    nuevasImagenes.value.splice(index, 1);
    form.nuevas_imagenes = nuevasImagenes.value.map(img => img.file);
};

const submitForm = () => {
    form.post(`/articulos/${props.articulo.id}`, {
        // Usar forceFormData si hay imágenes nuevas
        forceFormData: true,
        onSuccess: () => {
            // Limpiar previews de URL para evitar fugas de memoria
            nuevasImagenes.value.forEach(img => {
                URL.revokeObjectURL(img.preview);
            });
            nuevasImagenes.value = [];
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
                    <label class="block text-white text-sm font-bold mb-2">Imágenes actuales</label>

                    <!-- Mostrar mensaje si no hay imágenes -->
                    <div v-if="imagenesActuales.length === 0" class="text-gray-300 italic">
                        No hay imágenes asociadas a este artículo
                    </div>

                    <!-- Galería de imágenes actuales -->
                    <div v-else class="grid grid-cols-2 gap-4 mb-4">
                        <div v-for="imagen in imagenesActuales" :key="imagen.id" class="relative">
                            <img :src="imagen.ruta && imagen.ruta.startsWith('http') ? imagen.ruta : `/storage/${imagen.ruta}`"
                                class="h-40 w-full object-cover rounded" alt="Imagen del artículo">
                            <button type="button" @click="marcarImagenParaEliminar(imagen)"
                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <label class="block text-white text-sm font-bold mb-2">Nuevas imágenes</label>

                    <!-- Imágenes nuevas seleccionadas -->
                    <div v-if="nuevasImagenes.length > 0" class="grid grid-cols-2 gap-4 mb-4">
                        <div v-for="(imagen, index) in nuevasImagenes" :key="index" class="relative">
                            <img :src="imagen.preview" class="h-40 w-full object-cover rounded" alt="Nueva imagen">
                            <button type="button" @click="quitarNuevaImagen(index)"
                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Input para subir nuevas imágenes -->
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        type="file" @change="handleImageUpload" accept="image/*" multiple id="imagen"/>

                    <div v-if="form.errors.nuevas_imagenes" class="text-red-500 text-xs italic mt-4">
                        {{ form.errors.nuevas_imagenes }}
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
