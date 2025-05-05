<script setup>
import { Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import axios from "axios";
import "vue3-carousel/dist/carousel.css";
import {
    Carousel,
    Slide,
    Pagination,
    Navigation as CarouselNav,
} from "vue3-carousel";
import Navbar from "@/Components/Navbar.vue";

const props = defineProps({
    cartItems: Array,
});

const logout = () => {
    router.post(route("logout"));
};

// Función para determinar si la URL es completa (comienza con http:// o https://)
const isFullUrl = (url) => {
    return url && (url.startsWith("http://") || url.startsWith("https://"));
};

const checkout = async () => {
    console.log("Iniciando checkout con axios...");
    try {
        // Petición POST con axios
        const response = await axios.post("/cart/checkout");

        console.log("Respuesta del servidor:", response);

        if (response.data && response.data.url) {
            console.log("URL recibida:", response.data.url);
            // Redirige el navegador a la URL de Stripe
            window.location.href = response.data.url;
        } else {
            console.error(
                "No se recibió una URL válida en la respuesta:",
                response.data
            );
            alert("Error: No se pudo obtener la URL de pago.");
        }
    } catch (error) {
        console.error("Error durante la petición de checkout:", error);
        if (error.response) {
            console.error("Datos del error:", error.response.data);
            console.error("Estado del error:", error.response.status);
            alert(
                `Error ${error.response.status}: ${
                    error.response.data.error ||
                    "Ocurrió un error en el servidor."
                }`
            );
        } else if (error.request) {
            console.error(
                "No se recibió respuesta del servidor:",
                error.request
            );
            alert("Error: No se pudo conectar con el servidor.");
        } else {
            console.error("Error al configurar la petición:", error.message);
            alert("Error: Ocurrió un error inesperado.");
        }
    }
};

// Crear una copia local reactiva de los elementos del carrito

const localCartItems = ref([...props.cartItems]);

// Variable para almacenar el artículo seleccionado actualmente

const selectedItem = ref(null);

// Función para seleccionar un artículo al hacer clic
const seleccionarArticulo = (item) => {
    selectedItem.value = item;
};

// Inicializar con el primer artículo si existe
if (localCartItems.value.length > 0) {
    selectedItem.value = localCartItems.value[0];
}

// Función para incrementar cantidad (actualización optimista)
const incrementarCantidad = (item) => {
    // 1. Buscar el ítem en el carrito local
    const itemIndex = localCartItems.value.findIndex(
        (i) =>
            i.articulo.id === item.articulo.id && i.talla_id === item.talla_id
    );

    if (itemIndex !== -1) {
        // 2. Actualizar inmediatamente la UI
        localCartItems.value[itemIndex].cantidad += 1;

        // 3. Enviar la petición al servidor en segundo plano
        router.post(
            `/cart/${item.articulo.id}`,
            {
                talla_id: item.talla_id,
            },
            {
                preserveScroll: true,
                preserveState: true,
                onError: (errors) => {
                    // Si hay error, revertir el cambio en la UI
                    localCartItems.value[itemIndex].cantidad -= 1;
                    console.error("Error al incrementar cantidad:", errors);
                },
            }
        );
    }
};

// Función para decrementar cantidad (actualización optimista)
const decrementarCantidad = (item) => {
    // 1. Buscar el ítem en el carrito local
    const itemIndex = localCartItems.value.findIndex(
        (i) =>
            i.articulo.id === item.articulo.id && i.talla_id === item.talla_id
    );

    if (itemIndex !== -1 && localCartItems.value[itemIndex].cantidad > 1) {
        // 2. Actualizar inmediatamente la UI
        localCartItems.value[itemIndex].cantidad -= 1;

        // 3. Enviar la petición al servidor en segundo plano
        router.put(
            `/cart/${item.articulo.id}`,
            {
                talla_id: item.talla_id,
            },
            {
                preserveScroll: true,
                preserveState: true,
                onError: (errors) => {
                    // Si hay error, revertir el cambio en la UI
                    localCartItems.value[itemIndex].cantidad += 1;
                    console.error("Error al decrementar cantidad:", errors);
                },
            }
        );
    }
};

// Eliminar artículo del carrito (actualización optimista)
const eliminarDelCarrito = (item) => {
    // 1. Guardar el estado anterior para restaurar en caso de error
    const itemsAntes = [...localCartItems.value];

    // 2. Actualizar inmediatamente la UI
    localCartItems.value = localCartItems.value.filter(
        (i) =>
            !(
                i.articulo.id === item.articulo.id &&
                i.talla_id === item.talla_id
            )
    );

    // Si eliminamos el artículo seleccionado, actualizar el seleccionado
    if (selectedItem.value && selectedItem.value.id === item.id) {
        selectedItem.value =
            localCartItems.value.length > 0 ? localCartItems.value[0] : null;
    }

    // 3. Enviar la petición al servidor en segundo plano
    router.delete(`/cart/${item.articulo.id}`, {
        data: { talla_id: item.talla_id },
        preserveScroll: true,
        onError: (errors) => {
            // Si hay error, revertir el cambio en la UI
            localCartItems.value = itemsAntes;
            console.error("Error al eliminar artículo:", errors);
        },
    });
};

// Recalcular el precio total basado en el carrito local
const precioTotal = computed(() => {
    return localCartItems.value
        .reduce((sum, item) => {
            const precioFinal = obtenerPrecioFinal(item.articulo);
            return sum + item.cantidad * precioFinal;
        }, 0)
        .toFixed(2);
});

const obtenerPrecioFinal = (articulo) => {
    const descuento = articulo.descuento;
    const precio = articulo.precio;
    if (descuento && descuento > 0) {
        return (precio - precio * (descuento / 100)).toFixed(2);
    }
    return precio.toFixed(2);
};

// Calcular el conteo de elementos del carrito para mostrar en la barra de navegación
const cartItemCount = computed(() => {
    return localCartItems.value.length;
});
</script>

<template>
    <div
        class="bg-gray-50 text-gray-800 dark:bg-black dark:text-white min-h-screen"
    >
        <div
            class="flex flex-col items-start justify-center selection:bg-purple-500 selection:text-white dark:selection:bg-[#FF2D20]"
        >
            <Navbar :cart-items-count="cartItemCount" />

            <div
                class="px-8 flex flex-row min-w-full border-b border-gray-300 dark:border-white/20 bg-gradient-to-r from-purple-100 to-transparent dark:from-purple-900/10"
            >
                <h2
                    class="pt-6 pb-6 font-extrabold text-4xl text-gray-800 dark:text-white flex items-center"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-10 h-10 mr-3 text-purple-600"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
                        />
                    </svg>
                    Tu Carrito
                </h2>
            </div>

            <div class="flex flex-col md:flex-row w-full min-h-screen">
                <!-- Columna izquierda con los items del carrito - más estrecha en desktop -->
                <div
                    class="w-full md:w-2/5 p-4 md:p-6 border-r border-gray-300 dark:border-white/20 bg-gradient-to-b from-purple-50 to-transparent dark:from-purple-900/5"
                >
                    <div v-if="localCartItems.length > 0" class="space-y-4">
                        <!-- Elementos del carrito con efecto hover y selección -->
                        <div
                            v-for="item in localCartItems"
                            :key="item.id"
                            @click="seleccionarArticulo(item)"
                            class="bg-white dark:bg-gray-800/40 py-3 px-4 rounded-xl shadow-sm hover:shadow-md flex text-gray-800 dark:text-white border border-transparent transition-all duration-300 cursor-pointer"
                            :class="
                                selectedItem &&
                                selectedItem.articulo.id === item.articulo.id
                                    ? 'border-2 border-purple-500 scale-[1.02] shadow-md bg-purple-50 dark:bg-gray-800/60'
                                    : 'hover:border-purple-200 hover:bg-purple-50 dark:hover:bg-gray-800/50'
                            "
                        >
                            <div
                                class="flex-shrink-0 flex flex-col items-center justify-center ml-1"
                            >
                                <div
                                    class="relative w-14 h-14 overflow-hidden rounded-lg shadow-inner bg-gray-100 dark:bg-gray-700"
                                >
                                    <img
                                        class="w-full h-full object-cover"
                                        :src="
                                            item.articulo.imagenes &&
                                            item.articulo.imagenes.length > 0
                                                ? isFullUrl(
                                                      item.articulo.imagenes[0]
                                                          .ruta
                                                  )
                                                    ? item.articulo.imagenes[0]
                                                          .ruta
                                                    : `/storage/${item.articulo.imagenes[0].ruta}`
                                                : '/img/placeholder.webp'
                                        "
                                        alt="Imagen Articulo"
                                    />
                                    <div
                                        class="absolute inset-0 bg-gradient-to-tr from-black/10 to-transparent"
                                    ></div>
                                </div>
                            </div>

                            <div class="ml-4 flex-grow">
                                <div class="flex justify-between">
                                    <h2
                                        class="text-base font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{ item.articulo.nombre }}
                                    </h2>
                                    <button
                                        @click.stop="eliminarDelCarrito(item)"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors rounded-full hover:bg-red-50 dark:hover:bg-red-900/20 p-1"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>

                                <div class="flex items-center gap-2 mt-2">
                                    <div
                                        class="flex items-center border border-gray-300 rounded-full overflow-hidden"
                                    >
                                        <button
                                            @click.stop="
                                                decrementarCantidad(item)
                                            "
                                            type="button"
                                            class="flex items-center justify-center h-6 w-6 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 transition-all"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M20 12H4"
                                                />
                                            </svg>
                                        </button>

                                        <span
                                            class="px-3 py-1 text-gray-800 dark:text-white"
                                            >{{ item.cantidad }}</span
                                        >

                                        <button
                                            @click.stop="
                                                incrementarCantidad(item)
                                            "
                                            type="button"
                                            class="flex items-center justify-center h-6 w-6 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 transition-all"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 4v16m8-8H4"
                                                />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Mostrar la talla -->
                                    <div
                                        v-if="item.talla"
                                        class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md"
                                    >
                                        Talla: {{ item.talla.talla }}
                                    </div>

                                    <!-- Precio con descuento aplicado -->
                                    <div class="ml-auto">
                                        <div
                                            v-if="
                                                item.articulo.descuento &&
                                                item.articulo.descuento > 0
                                            "
                                            class="flex flex-col items-end"
                                        >
                                            <span
                                                class="line-through text-gray-500 dark:text-gray-400 text-xs"
                                                >{{
                                                    item.articulo.precio
                                                }}
                                                €</span
                                            >
                                            <p
                                                class="text-gray-900 dark:text-white font-semibold"
                                            >
                                                {{
                                                    obtenerPrecioFinal(
                                                        item.articulo
                                                    )
                                                }}
                                                €
                                            </p>
                                        </div>
                                        <p
                                            v-else
                                            class="text-gray-900 dark:text-white font-semibold"
                                        >
                                            {{ item.articulo.precio }} €
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de total y checkout con mejores estilos -->
                        <div
                            class="mt-6 p-6 bg-gradient-to-r from-purple-100 to-blue-50 dark:from-purple-900/30 dark:to-blue-900/20 rounded-xl shadow-sm border border-purple-200 dark:border-purple-400/10"
                        >
                            <div class="flex justify-between items-center mb-4">
                                <p
                                    class="text-xl font-bold text-gray-800 dark:text-white"
                                >
                                    Total:
                                </p>
                                <p
                                    class="text-2xl font-extrabold text-purple-700 dark:text-purple-400"
                                >
                                    {{ precioTotal }} €
                                </p>
                            </div>

                            <button
                                @click="checkout"
                                class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-5 h-5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"
                                    />
                                </svg>
                                Proceder al pago
                            </button>
                        </div>
                    </div>

                    <div
                        v-else
                        class="flex flex-col items-center justify-center h-64 mt-8 p-6 bg-white dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-16 h-16 text-gray-400 dark:text-gray-500 mb-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
                            />
                        </svg>
                        <p
                            class="text-center text-lg text-gray-600 dark:text-gray-400 mb-2"
                        >
                            Tu carrito está vacío
                        </p>
                        <p
                            class="text-center text-sm text-gray-500 dark:text-gray-500 mb-4"
                        >
                            ¡Añade algunos productos para comenzar!
                        </p>
                        <Link
                            href="/articulos"
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors flex items-center gap-2"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-5 h-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"
                                />
                            </svg>
                            Ver productos
                        </Link>
                    </div>
                </div>

                <!-- Columna derecha - detalle del artículo seleccionado -->
                <div class="w-full md:w-3/5 p-4 md:p-6">
                    <div
                        v-if="selectedItem"
                        class="bg-gradient-to-br from-white to-purple-50 dark:from-gray-800/80 dark:to-purple-900/30 rounded-xl p-6 shadow-lg text-gray-800 dark:text-white overflow-hidden border border-gray-200 dark:border-white/5"
                    >
                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Carousel de imágenes del artículo -->
                            <div class="w-full md:w-1/2 relative">
                                <div
                                    class="absolute -inset-1 bg-gradient-to-r from-purple-200 to-blue-200 dark:from-purple-600 dark:to-blue-600 rounded-xl blur-sm opacity-20 -z-10"
                                ></div>
                                <Carousel
                                    class="carousel-custom rounded-xl overflow-hidden shadow-md bg-white/90 dark:bg-black/40 backdrop-blur-sm"
                                    :items-to-show="1"
                                    :wrap-around="true"
                                    :autoplay="3000"
                                    v-if="
                                        selectedItem.articulo.imagenes &&
                                        selectedItem.articulo.imagenes.length >
                                            0
                                    "
                                >
                                    <Slide
                                        v-for="(imagen, index) in selectedItem
                                            .articulo.imagenes"
                                        :key="index"
                                    >
                                        <div
                                            class="h-64 md:h-80 flex items-center justify-center p-4"
                                        >
                                            <img
                                                :src="
                                                    isFullUrl(imagen.ruta)
                                                        ? imagen.ruta
                                                        : `/storage/${imagen.ruta}`
                                                "
                                                :alt="`Imagen ${index + 1} de ${
                                                    selectedItem.articulo.nombre
                                                }`"
                                                class="max-h-full max-w-full object-contain rounded-lg"
                                            />
                                        </div>
                                    </Slide>

                                    <template #addons>
                                        <CarouselNav />
                                        <Pagination
                                            v-if="
                                                selectedItem.articulo.imagenes
                                                    .length > 1
                                            "
                                        />
                                    </template>
                                </Carousel>

                                <!-- Imagen placeholder cuando no hay imágenes -->
                                <div
                                    v-else
                                    class="h-64 md:h-80 flex items-center justify-center p-4 bg-white/90 dark:bg-gray-800/50 backdrop-blur-sm rounded-xl"
                                >
                                    <img
                                        src="/img/placeholder.webp"
                                        alt="Imagen no disponible"
                                        class="max-h-full max-w-full object-contain rounded-lg opacity-70"
                                    />
                                </div>
                            </div>

                            <!-- Información detallada -->
                            <div
                                class="w-full md:w-1/2 p-4 md:p-6 backdrop-blur-sm bg-white/50 dark:bg-white/5 rounded-xl"
                            >
                                <h2
                                    class="text-2xl font-bold mb-2 text-gray-900 dark:text-white"
                                >
                                    {{ selectedItem.articulo.nombre }}
                                </h2>

                                <!-- Precio con descuento -->
                                <div class="flex items-center gap-2 mb-6">
                                    <div
                                        v-if="
                                            selectedItem.articulo.descuento &&
                                            selectedItem.articulo.descuento > 0
                                        "
                                        class="flex flex-col"
                                    >
                                        <p
                                            class="text-3xl font-bold text-purple-600 dark:text-purple-400"
                                        >
                                            {{
                                                obtenerPrecioFinal(
                                                    selectedItem.articulo
                                                )
                                            }}
                                            €
                                        </p>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="line-through text-gray-500 dark:text-gray-400 text-lg"
                                                >{{
                                                    selectedItem.articulo.precio
                                                }}
                                                €</span
                                            >
                                            <span
                                                class="text-sm font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 px-2 py-1 rounded-full"
                                            >
                                                -{{
                                                    selectedItem.articulo
                                                        .descuento
                                                }}%
                                            </span>
                                        </div>
                                    </div>
                                    <p
                                        v-else
                                        class="text-3xl font-bold text-purple-600 dark:text-purple-400"
                                    >
                                        {{ selectedItem.articulo.precio }} €
                                    </p>
                                    <span
                                        class="px-2 py-1 bg-green-50 text-green-600 dark:bg-green-100/20 dark:text-green-400 text-xs font-semibold rounded-full border border-green-200 dark:border-green-400/30"
                                        >En stock</span
                                    >
                                </div>

                                <div
                                    class="mb-6 border-t border-b border-gray-200 dark:border-white/10 py-4"
                                >
                                    <h3
                                        class="text-lg font-semibold mb-2 flex items-center text-gray-800 dark:text-white"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"
                                            />
                                        </svg>
                                        Descripción
                                    </h3>
                                    <div
                                        class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed article-content"
                                    >
                                        {{
                                            selectedItem.articulo
                                                .descripcion_short
                                        }}
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <h3
                                        class="text-lg font-semibold mb-3 flex items-center text-gray-800 dark:text-white"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
                                            />
                                        </svg>
                                        Cantidad en carrito
                                    </h3>
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="flex items-center border border-purple-300 dark:border-purple-400/30 rounded-lg overflow-hidden bg-white dark:bg-white/5"
                                        >
                                            <button
                                                @click="
                                                    decrementarCantidad(
                                                        selectedItem
                                                    )
                                                "
                                                type="button"
                                                class="flex items-center justify-center h-10 w-10 hover:bg-purple-100 dark:hover:bg-purple-500/20 transition-all text-gray-700 dark:text-white"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-5 w-5"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M20 12H4"
                                                    />
                                                </svg>
                                            </button>

                                            <span
                                                class="w-10 text-center py-1 text-lg font-semibold text-gray-800 dark:text-white"
                                                >{{
                                                    selectedItem.cantidad
                                                }}</span
                                            >

                                            <button
                                                @click="
                                                    incrementarCantidad(
                                                        selectedItem
                                                    )
                                                "
                                                type="button"
                                                class="flex items-center justify-center h-10 w-10 hover:bg-purple-100 dark:hover:bg-purple-500/20 transition-all text-gray-700 dark:text-white"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-5 w-5"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4v16m8-8H4"
                                                    />
                                                </svg>
                                            </button>
                                        </div>
                                        <span
                                            class="text-gray-600 dark:text-white/60 text-sm"
                                            >Subtotal:
                                            <span
                                                class="font-semibold text-gray-900 dark:text-white"
                                                >{{
                                                    (
                                                        selectedItem.cantidad *
                                                        obtenerPrecioFinal(
                                                            selectedItem.articulo
                                                        )
                                                    ).toFixed(2)
                                                }}
                                                €</span
                                            ></span
                                        >
                                    </div>
                                </div>

                                <!-- Mostrar la talla seleccionada -->
                                <div v-if="selectedItem.talla" class="mb-6">
                                    <h3
                                        class="text-lg font-semibold mb-3 flex items-center text-gray-800 dark:text-white"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M6 6h.008v.008H6V6Z"
                                            />
                                        </svg>
                                        Talla seleccionada
                                    </h3>
                                    <div
                                        class="inline-block px-4 py-2 bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium rounded-lg"
                                    >
                                        {{ selectedItem.talla.talla }}
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div
                                    class="flex flex-col sm:flex-row gap-3 mt-6"
                                >
                                    <button
                                        @click="
                                            eliminarDelCarrito(selectedItem)
                                        "
                                        class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-red-400 to-red-500 hover:from-red-500 hover:to-red-600 dark:from-red-500/80 dark:to-red-600/80 dark:hover:from-red-600 dark:hover:to-red-700 text-white rounded-lg transition-all duration-300 shadow-sm hover:shadow"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            />
                                        </svg>
                                        Eliminar
                                    </button>

                                    <Link
                                        :href="`/articulos/${selectedItem.articulo.id}`"
                                        class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-blue-400 to-blue-500 hover:from-blue-500 hover:to-blue-600 dark:from-blue-500/80 dark:to-blue-600/80 dark:hover:from-blue-600 dark:hover:to-blue-700 text-white rounded-lg transition-all duration-300 shadow-sm hover:shadow"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                            />
                                        </svg>
                                        Ver detalles
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mensaje cuando no hay artículo seleccionado -->
                    <div
                        v-else
                        class="h-full flex flex-col items-center justify-center p-10 bg-gradient-to-br from-purple-50 to-blue-50 dark:from-gray-900/50 dark:to-purple-900/20 rounded-xl border border-purple-100 dark:border-purple-300/10"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-16 h-16 text-purple-300 dark:text-purple-300/40 mb-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
                            />
                        </svg>
                        <p
                            class="text-lg text-gray-600 dark:text-gray-400 mb-2"
                        >
                            Selecciona un artículo para ver sus detalles
                        </p>
                        <p
                            class="text-sm text-gray-500 dark:text-gray-500 text-center max-w-md"
                        >
                            Haz clic en cualquier producto de tu carrito para
                            ver más información y opciones
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* Estilos personalizados para los botones de navegación del carrusel */
.carousel-custom {
    position: relative;
    overflow: hidden;
    /* Asegura que los elementos no se salgan del contenedor */
}

.carousel-custom .carousel__prev,
.carousel-custom .carousel__next {
    background-color: rgba(139, 92, 246, 0.6) !important;
    /* Color púrpura semitransparente */
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
    left: 8px;
    /* Posición desde el borde izquierdo */
}

.carousel-custom .carousel__next {
    right: 8px;
    /* Posición desde el borde derecho */
}

.carousel-custom .carousel__prev:hover,
.carousel-custom .carousel__next:hover {
    background-color: rgba(139, 92, 246, 0.9) !important;
    /* Púrpura más sólido al hover */
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
    content: "";
    position: absolute;
    inset: -2px;
    border-radius: 50%;
    background: linear-gradient(
        45deg,
        rgba(168, 85, 247, 0.4),
        rgba(79, 70, 229, 0.4)
    );
    opacity: 0;
    transition: opacity 0.3s ease;
}

.carousel-custom .carousel__pagination-button:hover::after {
    opacity: 1;
}

.carousel-custom .carousel__pagination-button--active {
    background-color: rgba(139, 92, 246, 1) !important;
    /* Color púrpura que combine con el tema */
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

/* Estilos adicionales para el contenido HTML formateado en descripciones */
.article-content h1,
.article-content h2,
.article-content h3 {
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

.article-content ul,
.article-content ol {
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

.article-content th,
.article-content td {
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

.dark .article-content th,
.dark .article-content td {
    border-color: #374151;
}
</style>
