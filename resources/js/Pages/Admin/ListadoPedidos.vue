<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    pedidos: Array,
    usuarios: Array
});

</script>

<template>

<div class="min-h-screen p-8 min-w-screen bg-zinc-900 text-white flex items-center flex-col">
    <h2 class="p-4 border border-slate-700 rounded-xl bg-slate-800">Listado de pedidos</h2>
    <ul class="p-8 flex flex-col w-full">
      <li class="bg-white p-6 rounded-lg shadow-md flex items-center text-black mb-2">
        <h2 class="text-lg font-semibold flex-1">Id de pedido</h2>
        <p class="text-blue-700 font-bold flex-1">Estado de pedido</p>
        <p class="text-blue-700 flex-1">Direccion de envio</p>
        <p class="text-blue-700 flex-1">Usuario</p>
        <p class="text-blue-700 flex-1">Acciones</p>
      </li>

      <li class="bg-white p-6 rounded-lg shadow-md flex items-center text-black mb-2" v-for="pedido in pedidos" :key="pedido.id">
        <h2 class="text-lg font-semibold flex-1">{{ pedido.id }}</h2>
        <p class="text-blue-700 font-bold flex-1">{{ pedido.estado }}</p>
        <p class="text-blue-700 flex-1">{{ pedido.direccion_envio }}</p>
        <p class="text-blue-700 flex-1">{{ pedido.user.name }}</p>
        <div class="flex items-center space-x-4 flex-1">
          <Link :href="`/pedidos/${pedido.id}/edit`" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Editar</Link>
          <Link :href="`/pedidos/${pedido.id}`" method="delete" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Eliminar</Link>
        </div>
      </li>
    </ul>
  </div>

</template>
