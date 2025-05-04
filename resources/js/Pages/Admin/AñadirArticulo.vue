<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Props para recibir las tallas disponibles
const props = defineProps({
    todasLasTallas: Array
});

// Vista previa de la imagen
const previewImage = ref('');

// Gestión de tallas
const tallasAsignadas = ref([]);
const tallaSeleccionada = ref('');
const stockTalla = ref(100);

// Tallas disponibles para añadir (excluye las que ya están asignadas)
const tallasDisponibles = computed(() => {
    if (!props.todasLasTallas) return [];

    // Filtrar las tallas que ya están asignadas al artículo
    const tallasAsignadasIds = tallasAsignadas.value.map(talla => talla.id);
    return props.todasLasTallas.filter(talla => !tallasAsignadasIds.includes(talla.id));
});

const form = useForm({
    nombre: '',
    imagen: null,
    categoria_id: '1',
    descripcion: '',
    descripcion_short: '',
    precio: 0,
    tallas: []
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

// Función para añadir una nueva talla al artículo
const agregarTalla = () => {
    if (!tallaSeleccionada.value) {
        alert('Por favor, selecciona una talla');
        return;
    }

    // Buscar la talla seleccionada en las disponibles
    const tallaSeleccionadaObj = props.todasLasTallas.find(talla => talla.id == tallaSeleccionada.value);
    if (!tallaSeleccionadaObj) return;

    // Añadir la talla a las asignadas con su stock
    tallasAsignadas.value.push({
        ...tallaSeleccionadaObj,
        stock: parseInt(stockTalla.value)
    });

    // Reiniciar los valores
    tallaSeleccionada.value = '';
    stockTalla.value = 100;
};

// Función para actualizar el stock de una talla
const actualizarStockTalla = (talla, nuevoStock) => {
    talla.stock = parseInt(nuevoStock);
};

// Función para eliminar una talla
const eliminarTalla = (tallaId) => {
    tallasAsignadas.value = tallasAsignadas.value.filter(talla => talla.id !== tallaId);
};

const submitForm = () => {
    // Preparar las tallas para enviar
    form.tallas = tallasAsignadas.value.map(talla => ({
        id: talla.id,
        stock: talla.stock
    }));

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

                <!-- Sección de tallas -->
                <div class="mb-6 border-t border-gray-500 pt-4">
                    <h2 class="text-lg font-bold mb-4">Gestión de tallas</h2>

                    <!-- Tallas asignadas -->
                    <div class="mb-4">
                        <label class="block text-white text-sm font-bold mb-2">Tallas asignadas</label>
                        <div v-if="tallasAsignadas.length === 0" class="text-gray-300 italic mb-2">
                            No hay tallas asignadas a este artículo
                        </div>
                        <div v-else class="bg-zinc-800 rounded-md p-3 mb-4">
                            <div v-for="talla in tallasAsignadas" :key="talla.id" class="flex items-center mb-2 pb-2 border-b border-zinc-700 last:border-0 last:mb-0 last:pb-0">
                                <div class="font-semibold text-white bg-purple-600 rounded-md px-3 py-1 mr-2">
                                    {{ talla.talla }}
                                </div>
                                <div class="flex-1 flex items-center">
                                    <label class="text-sm mr-2">Stock:</label>
                                    <input
                                        type="number"
                                        min="0"
                                        class="w-20 py-1 px-2 rounded text-gray-800"
                                        :value="talla.stock"
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
