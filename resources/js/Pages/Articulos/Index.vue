<script setup>
import { Head, Link, router } from '@inertiajs/vue3'

defineProps({
    articulos: Object,
});

const logout = () => {
    router.post(route('logout'));
};

const isFullUrl = (url) => {
    return url && (url.startsWith('http://') || url.startsWith('https://'));
};
</script>

<template>
    <div>
        <Head title="Artículos" />
        <div class="container mx-auto py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Artículos</h1>
                <Link href="/articulos/create" class="bg-blue-500 text-white px-4 py-2 rounded">Crear Artículo</Link>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="articulo in articulos" :key="articulo.id"
                    class="bg-white p-6 rounded-lg shadow-md flex flex-col">
                    <div>
                        <div class="flex justify-center">
                            <img class="w-full h-64 object-cover rounded-lg mb-4"
                                :src="articulo.imagenes && articulo.imagenes.length > 0
                                    ? (isFullUrl(articulo.imagenes[0].ruta) ? articulo.imagenes[0].ruta : `/${articulo.imagenes[0].ruta}`)
                                    : '/img/placeholder.webp'"
                                alt="Imagen Articulo">
                        </div>
                        <h2 class="text-xl font-bold mb-2">{{ articulo.titulo }}</h2>
                        <p class="text-gray-700">{{ articulo.descripcion }}</p>
                    </div>
                    <div class="mt-auto">
                        <Link :href="`/articulos/${articulo.id}`" class="bg-blue-500 text-white px-4 py-2 rounded">Ver Más</Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
