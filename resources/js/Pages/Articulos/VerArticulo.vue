<script setup>
import { Link, router } from '@inertiajs/vue3'
import 'vue3-carousel/dist/carousel.css';
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel';
import { ref, computed } from 'vue';
import Navbar from '@/Components/Navbar.vue'

const logout = () => {
    router.post(route('logout'));
};

const props = defineProps({
    articulo: Object,
    esFavorito: {
        type: Boolean,
        default: false
    }
});

// Usamos un ref para poder cambiar su estado reactivamente cuando el usuario hace clic
const esFavoritoActual = ref(props.esFavorito);
const selectedTallaId = ref(null); // Variable para almacenar la talla seleccionada
const tallaError = ref(''); // Variable para mostrar mensajes de error

// Mostrar mensaje si no hay tallas disponibles o con stock
const hayTallasDisponibles = computed(() => {
    if (!props.articulo.tallas || props.articulo.tallas.length === 0) return false;
    return props.articulo.tallas.some(talla => talla.pivot.stock > 0);
});

// Función para añadir al carrito con talla seleccionada
const addToCart = () => {
    // Validar que se ha seleccionado una talla
    if (!selectedTallaId.value) {
        tallaError.value = 'Por favor, selecciona una talla';
        return;
    }

    // Validar que la talla seleccionada tiene stock
    const tallaSeleccionada = props.articulo.tallas.find(t => t.id === selectedTallaId.value);
    if (!tallaSeleccionada || tallaSeleccionada.pivot.stock <= 0) {
        tallaError.value = 'Esta talla no está disponible';
        return;
    }

    // Enviar al servidor con la talla seleccionada
    router.post(route('cart.store', { id: props.articulo.id }), {
        talla_id: selectedTallaId.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Limpiar mensaje de error y talla seleccionada después de añadir exitosamente
            tallaError.value = '';
            selectedTallaId.value = null;
        },
        onError: (errors) => {
            // Mostrar error de validación
            tallaError.value = errors.talla_id || 'Ha ocurrido un error';
        }
    });
};

const toggleFavorito = () => {
    // Actualización optimista: cambiamos el estado visual inmediatamente
    esFavoritoActual.value = !esFavoritoActual.value;

    // Guardamos el estado anterior por si hay errores
    const estadoAnterior = esFavoritoActual.value;

    // Enviamos la petición al servidor
    router.post(route('favoritos.toggle', { id: props.articulo.id }), {}, {
        preserveScroll: true,
        onError: () => {
            // En caso de error, revertimos el cambio
            esFavoritoActual.value = !estadoAnterior;
        }
    });
};

// Función para formatear la descripción, incluyendo soporte para
// párrafos, listas, negritas y otros formatos básicos
const formatearDescripcion = computed(() => {
    if (!props.articulo.descripcion) return 'Este producto no tiene descripción.';

    let descripcion = props.articulo.descripcion;

    // Paso 1: Escapar HTML para prevenir inyecciones
    const escapeHTML = (unsafe) => {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    };

    descripcion = escapeHTML(descripcion);

    // Paso 2: Convertir saltos de línea en párrafos HTML
    descripcion = descripcion
        .split('\n\n')
        .map(p => p.trim())
        .filter(p => p.length > 0)
        .map(p => `<p>${p}</p>`)
        .join('');

    // Paso 3: Convertir saltos de línea simples en <br>
    descripcion = descripcion.replace(/\n/g, '<br>');

    // Paso 4: Formatear listas (si comienzan con - o *)
    descripcion = descripcion.replace(/<p>(\s*[-*]\s+.*?)<\/p>/gs, (match, list) => {
        const items = list.split(/\n(?=\s*[-*]\s+)/g);
        return '<ul class="list-disc pl-5 my-2">' +
            items.map(item => `<li>${item.replace(/^\s*[-*]\s+/, '')}</li>`).join('') +
            '</ul>';
    });

    // Paso 5: Formato para negritas con ** o __
    descripcion = descripcion.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    descripcion = descripcion.replace(/__(.*?)__/g, '<strong>$1</strong>');

    // Paso 6: Formato para cursiva con * o _
    descripcion = descripcion.replace(/\*(.*?)\*/g, '<em>$1</em>');
    descripcion = descripcion.replace(/_(.*?)_/g, '<em>$1</em>');

    return descripcion;
});

const isFullUrl = (url) => {
    return url && (url.startsWith('http://') || url.startsWith('https://'));
};

const imagenes = computed(() => {
    if (props.articulo.imagenes && props.articulo.imagenes.length > 0) {
        return props.articulo.imagenes.map(img => ({
            src: isFullUrl(img.ruta) ? img.ruta : `/storage/${img.ruta}`,
            alt: `Imagen de ${props.articulo.nombre}`
        }));
    } else {
        return [{
            src: '/img/placeholder.webp',
            alt: 'Imagen no disponible'
        }];
    }
});

const responsiveOptions = ref([
    {
        breakpoint: '1024px',
        numVisible: 5
    },
    {
        breakpoint: '768px',
        numVisible: 3
    },
    {
        breakpoint: '560px',
        numVisible: 1
    }
]);

const precioConDescuento = computed(() => {
    if (props.articulo.descuento && props.articulo.descuento > 0) {
        return (props.articulo.precio * (1 - props.articulo.descuento / 100)).toFixed(2);
    }
    return props.articulo.precio.toFixed(2);
});

</script>

<template>
    <div class="bg-gray-50 text-gray-800 dark:bg-black dark:text-white min-h-screen">
        <div class="flex flex-col selection:bg-purple-500 selection:text-white dark:selection:bg-[#FF2D20]">
            <Navbar />

            <!-- Título del producto con estilo actualizado -->
            <div class="px-8 flex flex-row min-w-full border-b border-gray-300 dark:border-white/20 bg-gradient-to-r from-purple-50 to-transparent dark:from-purple-900/10">
                <h2 class="pt-6 pb-6 font-extrabold text-4xl text-gray-800 dark:text-white flex items-center">
                    {{ articulo.nombre }}
                    <span class="ml-4 text-sm font-medium px-3 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300 rounded-full">
                        {{ articulo.categoria && articulo.categoria.length > 0 ? articulo.categoria[0].nombre : 'Sin categoría' }}
                    </span>
                </h2>
            </div>

            <main class="w-full p-4 mt-6 container mx-auto">
                <div class="flex flex-col md:flex-row gap-8">
                    <!-- Sección de imágenes con estilos actualizados -->
                    <div class="w-full md:w-2/3 lg:w-3/5">
                        <div class="bg-white dark:bg-gray-800/40 rounded-xl shadow-sm p-4 md:p-6">
                            <Carousel
                                class="carousel-custom rounded-xl overflow-hidden"
                                ref="carousel"
                                :items-to-show="1"
                                :wrap-around="true"
                            >
                                <Slide v-for="(imagen, index) in imagenes" :key="index">
                                    <div class="h-[400px] md:h-[500px] flex items-center justify-center">
                                        <img
                                            :src="imagen.src"
                                            :alt="imagen.alt"
                                            class="max-h-full max-w-full object-contain rounded-lg"
                                        />
                                    </div>
                                </Slide>
                                <template #addons>
                                    <Navigation />
                                    <Pagination />
                                </template>
                            </Carousel>

                            <!-- Miniaturas de navegación mejoradas -->
                            <div class="flex mt-4 overflow-x-auto space-x-2 p-2 pb-4">
                                <div
                                    v-for="(imagen, index) in imagenes"
                                    :key="index"
                                    class="flex-shrink-0 cursor-pointer rounded-md overflow-hidden border-2 transition-all duration-200"
                                    :class="{'border-purple-500 shadow-md': $refs.carousel && $refs.carousel.currentSlide === index, 'border-transparent hover:border-purple-200 dark:hover:border-gray-700': $refs.carousel && $refs.carousel.currentSlide !== index}"
                                    @click="$refs.carousel.slideTo(index)"
                                >
                                    <img
                                        :src="imagen.src"
                                        :alt="imagen.alt"
                                        class="w-16 h-16 object-cover"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de información del producto con estilos mejorados -->
                    <div class="w-full md:w-1/3 lg:w-2/5 flex flex-col">
                        <div class="bg-gradient-to-br from-white to-purple-50 dark:from-gray-800/80 dark:to-purple-900/30 rounded-xl p-6 shadow-lg text-gray-800 dark:text-white border border-gray-200 dark:border-white/5 h-full">
                            <div class="mb-8">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90 mb-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                                    Descripción del producto
                                </h2>
                                <div class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed article-content"
                                     v-html="formatearDescripcion">
                                </div>
                            </div>

                            <div class="mt-auto">
                                <div class="mb-6 border-t border-b border-gray-200 dark:border-white/10 py-4">
                                    <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-white">Precio</h3>

                                    <div v-if="articulo.descuento && articulo.descuento > 0" class="flex items-center">
                                        <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                                            {{ precioConDescuento }} €
                                        </p>
                                        <div class="ml-3 flex flex-col">
                                            <span class="line-through text-gray-500 dark:text-gray-400">{{ articulo.precio }} €</span>
                                            <span class="text-sm font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 px-2 py-1 rounded-full">
                                                -{{ articulo.descuento }}%
                                            </span>
                                        </div>
                                    </div>
                                    <p v-else class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                                        {{ articulo.precio }} €
                                    </p>

                                    <p v-if="hayTallasDisponibles" class="text-sm text-green-600 dark:text-green-400 mt-1">
                                        ✓ En stock
                                    </p>
                                    <p v-else class="text-sm text-red-600 dark:text-red-400 mt-1">
                                        ✕ Sin stock disponible
                                    </p>
                                </div>

                                <!-- Selector de tallas -->
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-white">
                                        Selecciona tu talla
                                    </h3>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            v-for="talla in articulo.tallas"
                                            :key="talla.id"
                                            @click="selectedTallaId = talla.id"
                                            :disabled="talla.pivot.stock <= 0"
                                            :class="[
                                                'px-4 py-2 border rounded-md transition-colors font-medium',
                                                selectedTallaId === talla.id ? 'bg-purple-600 text-white border-purple-600' : 'border-gray-300',
                                                talla.pivot.stock <= 0 ? 'opacity-50 cursor-not-allowed bg-gray-100 text-gray-500' : 'hover:border-purple-300'
                                            ]"
                                        >
                                            {{ talla.talla }}
                                            <span v-if="talla.pivot.stock <= 0" class="text-xs ml-1">(Agotada)</span>
                                        </button>
                                    </div>
                                    <p v-if="tallaError" class="text-red-500 text-sm mt-2">{{ tallaError }}</p>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-3">
                                    <button
                                        @click="addToCart"
                                        :disabled="!hayTallasDisponibles"
                                        :class="[
                                            'flex-1 font-semibold py-3 px-4 rounded-lg transition-all duration-300 shadow-sm hover:shadow flex items-center justify-center',
                                            hayTallasDisponibles ?
                                                'bg-purple-600 hover:bg-purple-700 text-white' :
                                                'bg-gray-400 cursor-not-allowed text-white'
                                        ]"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                        Añadir al carrito
                                    </button>

                                    <Link
                                        v-if="$page.props.auth.user"
                                        href="#"
                                        @click.prevent="toggleFavorito"
                                        class="bg-white hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 border border-gray-300 dark:border-transparent text-gray-700 dark:text-white font-semibold py-3 px-5 rounded-lg transition-all duration-300 shadow-sm hover:shadow flex items-center justify-center"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="esFavoritoActual ? 'text-pink-500' : 'text-gray-600 dark:text-gray-300'" :fill="esFavoritoActual ? 'currentColor' : 'none'"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer minimalista -->
            <footer class="bg-white dark:bg-gray-900 py-8 border-t border-gray-200 dark:border-gray-800 mt-12">
                <div class="container mx-auto px-4 text-center">
                    <p class="text-gray-600 dark:text-gray-400">© 2025 Caveman - Todos los derechos reservados</p>
                </div>
            </footer>
        </div>
    </div>
</template>

<style>
/* Estilos personalizados para los botones de navegación del carrusel */
.carousel-custom {
    position: relative;
    overflow: hidden; /* Asegura que los elementos no se salgan del contenedor */
}

.carousel-custom .carousel__prev,
.carousel-custom .carousel__next {
    background-color: rgba(139, 92, 246, 0.6) !important; /* Color púrpura semitransparente */
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white !important;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 5;
    backdrop-filter: blur(3px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.carousel-custom .carousel__prev {
    left: 8px; /* Posición desde el borde izquierdo */
}

.carousel-custom .carousel__next {
    right: 8px; /* Posición desde el borde derecho */
}

.carousel-custom .carousel__prev:hover,
.carousel-custom .carousel__next:hover {
    background-color: rgba(139, 92, 246, 0.9) !important; /* Púrpura más sólido al hover */
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
}

/* Aseguramos que los iconos dentro de los botones sean blancos */
.carousel-custom .carousel__prev i,
.carousel-custom .carousel__next i {
    color: white !important;
    font-size: 1.2rem;
}

/* Estilos para la paginación */
.carousel-custom .carousel__pagination {
    margin-top: 1rem;
    padding: 0.5rem;
    background-color: rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(5px);
    border-radius: 20px;
    display: inline-flex;
    position: relative;
    z-index: 10;
}

.carousel-custom .carousel__pagination-button {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin: 0 5px;
    background-color: rgba(255, 255, 255, 0.4);
    transition: all 0.3s ease;
    position: relative;
}

.carousel-custom .carousel__pagination-button::after {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 50%;
    background: linear-gradient(45deg, rgba(168, 85, 247, 0.4), rgba(79, 70, 229, 0.4));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.carousel-custom .carousel__pagination-button:hover::after {
    opacity: 1;
}

.carousel-custom .carousel__pagination-button--active {
    background-color: rgba(139, 92, 246, 1) !important; /* Color púrpura que combine con el tema */
    transform: scale(1.2);
    box-shadow: 0 0 10px rgba(139, 92, 246, 0.6);
}

/* Animación suave para las diapositivas */
.carousel__slide {
    transition: opacity 0.5s ease;
}

/* Efecto de escala al pasar el ratón sobre la imagen */
.carousel__slide img {
    transition: transform 0.3s ease;
}

.carousel__slide:hover img {
    transform: scale(1.03);
}

/* Estilos adicionales para el contenido HTML */
.article-content h1, .article-content h2, .article-content h3 {
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    font-weight: 600;
}

.article-content h1 {
    font-size: 1.5rem;
}

.article-content h2 {
    font-size: 1.25rem;
}

.article-content h3 {
    font-size: 1.125rem;
}

.article-content p {
    margin-bottom: 1rem;
}

.article-content ul, .article-content ol {
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}

.article-content ul {
    list-style-type: disc;
}

.article-content ol {
    list-style-type: decimal;
}

.article-content a {
    color: #8b5cf6;
    text-decoration: underline;
}

.article-content table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1rem;
}

.article-content th, .article-content td {
    border: 1px solid #e5e7eb;
    padding: 0.5rem;
}

.article-content blockquote {
    border-left: 4px solid #8b5cf6;
    padding-left: 1rem;
    font-style: italic;
    margin-bottom: 1rem;
}

.dark .article-content a {
    color: #a78bfa;
}

.dark .article-content th, .dark .article-content td {
    border-color: #374151;
}
</style>
