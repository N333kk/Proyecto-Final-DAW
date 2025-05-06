<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    articulo: Object,
    todasLasTallas: Array
});

// Almacenamos todas las imágenes actuales
const imagenesActuales = ref(props.articulo.imagenes || []);

// Almacenamos las nuevas imágenes que se van a subir
const nuevasImagenes = ref([]);

// Almacenamos los IDs de las imágenes a eliminar
const imagenesAEliminar = ref([]);

// Gestión de tallas
const tallasActuales = ref(props.articulo.tallas || []);
const tallaSeleccionada = ref('');
const stockTalla = ref(100);

// Tallas disponibles para añadir (excluye las que ya están asignadas)
const tallasDisponibles = computed(() => {
    if (!props.todasLasTallas) return [];

    // Filtrar las tallas que ya están asignadas al artículo
    const tallasAsignadasIds = tallasActuales.value.map(talla => talla.id);
    return props.todasLasTallas.filter(talla => !tallasAsignadasIds.includes(talla.id));
});

const form = useForm({
    nombre: props.articulo.nombre,
    categoria_id: props.articulo.categoria_id || '1',
    descripcion: props.articulo.descripcion,
    descripcion_short: props.articulo.descripcion_short,
    precio: props.articulo.precio,
    nuevas_imagenes: [],
    imagenes_a_eliminar: [],
    tallas: [],
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

// Función para actualizar el stock de una talla
const actualizarStockTalla = (talla, nuevoStock) => {
    // Actualizar el stock en la UI
    talla.pivot.stock = parseInt(nuevoStock);
};

// Función para añadir una nueva talla al artículo
const agregarTalla = () => {
    if (!tallaSeleccionada.value) {
        alert('Por favor, selecciona una talla');
        return;
    }

    // Buscar la talla seleccionada en las disponibles
    const tallaSeleccionadaObj = props.todasLasTallas.find(talla => talla.id == tallaSeleccionada.value);
    if (!tallaSeleccionadaObj) return;

    // Añadir la talla a las actuales con su stock
    tallasActuales.value.push({
        ...tallaSeleccionadaObj,
        pivot: { stock: parseInt(stockTalla.value) }
    });

    // Reiniciar los valores
    tallaSeleccionada.value = '';
    stockTalla.value = 100;
};

// Función para eliminar una talla del artículo
const eliminarTalla = (tallaId) => {
    tallasActuales.value = tallasActuales.value.filter(talla => talla.id !== tallaId);
};

const submitForm = () => {
    // Preparar los datos de tallas para enviar
    form.tallas = tallasActuales.value.map(talla => ({
        id: talla.id,
        stock: talla.pivot.stock
    }));

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
<Head title="Editar Articulo" />
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

                <!-- Sección de tallas -->
                <div class="mb-6 border-t border-gray-500 pt-4">
                    <h2 class="text-lg font-bold mb-4">Gestión de tallas</h2>

                    <!-- Tallas actuales del artículo -->
                    <div class="mb-4">
                        <label class="block text-white text-sm font-bold mb-2">Tallas asignadas</label>
                        <div v-if="tallasActuales.length === 0" class="text-gray-300 italic mb-2">
                            No hay tallas asignadas a este artículo
                        </div>
                        <div v-else class="bg-zinc-800 rounded-md p-3 mb-4">
                            <div v-for="talla in tallasActuales" :key="talla.id" class="flex items-center mb-2 pb-2 border-b border-zinc-700 last:border-0 last:mb-0 last:pb-0">
                                <div class="font-semibold text-white bg-purple-600 rounded-md px-3 py-1 mr-2">
                                    {{ talla.talla }}
                                </div>
                                <div class="flex-1 flex items-center">
                                    <label class="text-sm mr-2">Stock:</label>
                                    <input
                                        type="number"
                                        min="0"
                                        class="w-20 py-1 px-2 rounded text-gray-800"
                                        :value="talla.pivot.stock"
                                        @input="actualizarStockTalla(talla, $event.target.value)"
                                    />
                                </div>
                                <button
                                    type="button"
                                    @click="eliminarTalla(talla.id)"
                                    class="ml-2 bg-red-600 hover:bg-red-700 text-white rounded p-1"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Añadir nuevas tallas -->
                    <div class="mb-4">
                        <label class="block text-white text-sm font-bold mb-2">Añadir nueva talla</label>
                        <div class="flex items-center">
                            <div class="flex-1 mr-2">
                                <select
                                    v-model="tallaSeleccionada"
                                    class="w-full py-2 px-3 bg-zinc-800 border border-zinc-600 rounded text-white"
                                >
                                    <option value="">Seleccionar talla</option>
                                    <option v-for="talla in tallasDisponibles" :key="talla.id" :value="talla.id">
                                        {{ talla.talla }}
                                    </option>
                                </select>
                            </div>
                            <div class="flex items-center mr-2">
                                <label class="text-sm mr-2">Stock:</label>
                                <input
                                    type="number"
                                    min="0"
                                    class="w-20 py-2 px-3 bg-zinc-800 border border-zinc-600 rounded text-white"
                                    v-model="stockTalla"
                                />
                            </div>
                            <button
                                type="button"
                                @click="agregarTalla"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                            >
                                Añadir
                            </button>
                        </div>
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
