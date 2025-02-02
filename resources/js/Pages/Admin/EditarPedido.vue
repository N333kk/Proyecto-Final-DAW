<script setup>

import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    pedido: Object,
});

const form = useForm({
    direccion_envio: props.pedido.direccion_envio,
    estado: props.pedido.estado,
});

</script>

<template>

<div class="min-h-screen p-8 min-w-screen bg-zinc-900 text-white flex items-center flex-col">
        <div class="px-16 py-8 bg-gradient-to-b from-zinc-700 to-zinc-500 border rounded-xl border-slate-400 text-white flex items-center flex-col">

            <h1 class="font-bold text-lg mb-4"> Editar Pedido </h1>

            <form @submit.prevent="form.put(`/pedidos/${props.pedido.id}`)">
                <div class="mb-4">
                <label class="block text-white text-sm font-bold mb-2" for="direccion_envio">Direccion de Envio</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        type="text"
                        v-model="form.direccion_envio"
                        id="direccion_envio"
                        required
                        autocomplete="address"/>
                <div v-if="form.errors.direccion_envio" class="text-red-500 text-xs italic mt-4">
                 {{ form.errors.direccion_envio }}
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-white text-sm font-bold mb-2" for="estado">Estado</label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        v-model="form.estado"
                        id="estado"
                        required>
                    <option>Pendiente</option>
                    <option>Enviado</option>
                    <option>Completado</option>>
                </select>
                <div v-if="form.errors.estado" class="text-red-500 text-xs italic mt-4">
                    {{ form.errors.estado }}
                </div>
            </div>

            <div>

            </div>
            <button type="submit" :disabled="form.processing" class="bg-zinc-800 hover:bg-zinc-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Editar Pedido</button>
        </form>
    </div>

</div>
</template>
