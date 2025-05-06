<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3';

const props = defineProps({
    talla: Object,
});

const form = useForm({
    talla: props.talla.talla,
    _method: 'put',
});

const submit = () => {
    form.post(route('tallas.update', props.talla.id));
};
</script>

<template>
    <Head title="Editar talla" />

    <div class="min-h-screen bg-zinc-900 text-white p-8">
        <div class="max-w-md mx-auto">
            <h1 class="text-2xl font-bold mb-6">Editar Talla</h1>

            <div class="bg-zinc-800 rounded-lg shadow-lg p-6">
                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <label for="talla" class="block text-sm font-medium text-zinc-300 mb-1">Nombre de la Talla</label>
                        <input
                            type="text"
                            id="talla"
                            v-model="form.talla"
                            class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                            required
                        />
                        <p v-if="form.errors.talla" class="text-red-500 text-xs mt-1">{{ form.errors.talla }}</p>
                    </div>

                    <div class="flex justify-between mt-6">
                        <Link
                            :href="route('tallas.index')"
                            class="px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-white rounded-md transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Guardando...' : 'Actualizar Talla' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
